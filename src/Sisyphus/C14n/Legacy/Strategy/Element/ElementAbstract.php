<?php
/**
 * Generic DOM element node processing strategy
 *
 * @author Konstantin Burnaev <kbourn@gmail.com>
 * @copyright Copyright (c) 2013, Konstantin Burnaev
 * @license http://opensource.org/licenses/MIT MIT
 */

/**
 * Generic DOM element node processing strategy
 *
 * Note  that  (depending  on  the document  subset  expression)  even
 * element  not included  in the  document subset  may have  non-empty
 * canonical presentation, if some  of their descendants, attribute or
 * namespace nodes are in the subset.
 */
abstract class Sisyphus_C14n_Legacy_Strategy_Element_ElementAbstract
    implements Sisyphus_C14n_Legacy_Strategy_StrategyInterface
{
    const DEFAULT_NAMESPACE_LOCAL_NAME = 'xmlns';
    const XML_NAMESPACE_URI = 'http://www.w3.org/XML/1998/namespace';
    const XML_NAMESPACE_PREFIX = 'xml';

    /**
     * Document subset object
     *
     * @var Sisyphus_C14n_Legacy_Nodeset
     */
    private $_nodeset;

    /**
     * Canonicalization context object
     *
     * @var Sisyphus_C14n_Context
     */
    private $_context;

    /**
     * Initializes the strategy instance
     *
     * @param Sisyphus_C14n_Context $context canonicalization context
     *
     * @param Sisyphus_C14n_Legacy_Nodeset $nodeset document subset
     */
    public function __construct(
        Sisyphus_C14n_Context $context,
        Sisyphus_C14n_Legacy_Nodeset $nodeset)
    {
        $this->setNodeset($nodeset)
             ->setContext($context);
    }

    /**
     * Generates canonical string presentation of an XML Element
     *
     * @see  Sisyphus_C14n_Legacy_Strategy_Element::canonicalize()
     * the description of the canonicalization algorithm
     *
     * @param DOMElement $node DOM element being canonicalized
     *
     * @return string canonical presentation
     */
    public function canonicalize($node)
    {
        $namespaceString = $this->generateNamespaceString($node);
        $attributeString = $this->generateAttributeString($node);

        $strategy = new Sisyphus_C14n_Legacy_Strategy_Dispatcher(
            $this->getContext(),
            $this->getNodeset()
        );

        $nestedString = '';
        foreach ($node->childNodes as $child) {
            $nestedString .= $strategy->canonicalize($child);
        };

        return $this->generateOutputString(
            $node->nodeName,
            $namespaceString,
            $attributeString,
            $nestedString
        );

    }

    /*
     * Implementation details
     */

    /**
     * Interface  definition  for  the  string  generation  algorithm.
     * Implementation depends  of whether current node  is included to
     * or excluded from the document subset.
     *
     * @param string $nodeName XML node name
     *
     * @param string $namespaceString string presentation of the
     * namespace axis
     *
     * @param string $attributeString string presentation of the
     * attribute axis
     *
     * @param   string  $nestedString   string  presentation   of  the
     * descendant axis
     *
     * @return string canonical presentation of the current node generated from
     * the above parts.
     */
    abstract protected function generateOutputString(
        $nodeName,
        $namespaceString,
        $attributeString,
        $nestedString);

    /**
     * Generates canonical string presentation of the namespace axis.
     *
     * @see  Sisyphus_C14n_Legacy_Strategy_Element::canonicalize()
     * the description of the canonicalization algorithm
     *
     * @param DOMElement $node base node for the axis
     *
     * @return string canonicalized namespace axis
     */
    protected function generateNamespaceString(DOMElement $node)
    {
        $orderedNamespaces = $this->sortNamespaces(
            iterator_to_array($this->getNodeNamespaces($node))
        );

        $namespaceString = '';
        $namespaceStrategy = new Sisyphus_C14n_Legacy_Strategy_Namespace(
            $this->getNodeset(),
            $this->getContext()->isExclusive(),
            $this->getContext()->getInclusiveNamespaces()
        );

        foreach ($orderedNamespaces as $namespaceNode) {
            // Skip 'xml' namespace
            if ($namespaceNode->localName == self::XML_NAMESPACE_PREFIX
                && $namespaceNode->nodeValue == self::XML_NAMESPACE_URI) {
                continue;
            };

            if (!$this->getNodeset()
                      ->isIncluded($namespaceNode)) {
                continue;
            };

            // Default namespace rules
            if ($namespaceNode->localName == self::DEFAULT_NAMESPACE_LOCAL_NAME
                && $namespaceNode->nodeValue == '') {

                if (!$this->getNodeset()
                          ->isIncluded($node)) {
                    continue;
                };

                if (!$this->hasVisibleAncestorWithDefaultNamespace($node)) {
                    continue;
                };
            };

            $namespaceString .= $namespaceStrategy->canonicalize(
                $namespaceNode
            );
        };

        return $namespaceString;
    }

    /**
     * Generates canonical string presentation of the attribute axis
     *
     * @see  Sisyphus_C14n_Legacy_Strategy_Element::canonicalize()
     * the description of the canonicalization algorithm
     *
     * @param DOMElement $node base node for the axis
     *
     * @return string canonicalized attribute axis
     */
    protected function generateAttributeString(DOMElement $node)
    {
        if ($this->getNodeset()->isIncluded($node)) {
            $effectiveAttrs = $this->getAttributesInXmlNamespaceWithInheritance(
                $node->parentNode
            );
        } else {
            $effectiveAttrs = array();
        };

        $visibleAttrs = array();
        foreach ($node->attributes as $attr) {
            if ($this->getNodeset()
                     ->isIncluded($attr)) {
                $visibleAttrs[] = $attr;
            };
        };

        $orderedAttributes = $this->sortAttributes(
            array_merge(
                array_values($effectiveAttrs),
                $visibleAttrs
            )
        );

        $attributeString = '';
        $attributeStrategy = new Sisyphus_C14n_Legacy_Strategy_Attribute();
        foreach ($orderedAttributes as $attributeNode) {
            $attributeString .= $attributeStrategy->canonicalize(
                $attributeNode
            );
        };

        return $attributeString;
    }

    /**
     * Sorts namespace nodes in namespace prefix lexicographical order.
     *
     * @param DOMNameSpaceNode[] $nodeset list of namespace nodes to sort
     *
     * @return DOMNameSpaceNode[] sorted list of namespace nodes
     */
    protected function sortNamespaces(array $nodeset)
    {
        $namespaces = array();

        foreach ($nodeset as $node) {
            $namespaces[$node->nodeName] = $node;
        };

        ksort($namespaces);
        return array_values($namespaces);
    }

    /**
     * Extracts a namespace axis for a selected DOM element
     *
     * @param  DOMElement  $node  DOM  element  used  as  a  base  for
     * namespace axis
     *
     * @return DOMNodeList list of namespace nodes
     */
    protected function getNodeNamespaces(DOMElement $node)
    {
        $xpath = new DOMXPath($node->ownerDocument);
        return $xpath->query('namespace::*', $node);
    }

    /**
     * Sorts a list of attribute  nodes in lexicographic order defined
     * by attribute namespace URL and attribute node local name.
     *
     * @param DOMAttr[] attribute list
     *
     * @return DOMAttr[] sorted attribute list
     */
    protected function sortAttributes(array $nodeset)
    {
        $attributes = array();

        foreach ($nodeset as $node) {
            // Note: separator (':') should go before letters in
            // lexicographical order
            $attributes[$node->namespaceURI . ':' . $node->name] = $node;
        };

        ksort($attributes);
        return array_values($attributes);
    }

    /**
     * Checks  if a  selected node  has  an visible  (included in  the
     * document  subset)  ancestor  with   default  namespace  set  to
     * non-empty value.
     *
     * @param DOMElement $node base node
     *
     * @return boolean flag indicating that this node has visible ancestor
     * with default namespace set to non-empty value
     */
    protected function hasVisibleAncestorWithDefaultNamespace(DOMElement $node)
    {
        $visibleAncestor = $this->getNodeset()->getVisibleAncestor($node);
        if (!$visibleAncestor) {
            return false;
        };

        foreach ($this->getNodeNamespaces($visibleAncestor) as $namespaceNode) {
            if ($namespaceNode->localName == self::DEFAULT_NAMESPACE_LOCAL_NAME) {
                return $namespaceNode->nodeValue != '';
            };
        };

        return false;
    }

    /**
     * Collects a list of attributes in 'xml' namespace (e.g. xml:space), taking into
     * account the whole path from selected node to the document root.
     *
     * @param $node selected DOM node
     *
     * @return DOMAttr[] a list of matching attribute nodes
     */
    protected function getAttributesInXmlNamespaceWithInheritance(DOMNode $node)
    {
        if ($node->nodeType != XML_ELEMENT_NODE) {
            return array();
        };

        return array_merge(
            $this->getAttributesInXmlNamespaceWithInheritance($node->parentNode),
            $this->getAttributesInXmlNamespace($node)
        );
    }

    /**
     * Extracts all attributes in 'xml' namespace (e.g. xml:space) for
     * a selected node.
     *
     * @param DOMNode $node selected DOM node
     *
     * @return DOMAttr[] list of attributes
     */
    protected function getAttributesInXmlNamespace(DOMNode $node)
    {
        $attributes = array();

        foreach ($node->attributes as $attr) {
            if ($attr->namespaceURI != self::XML_NAMESPACE_URI) {
                continue;
            };

            $attributes[$attr->localName] = $attr;
        };

        return $attributes;
    }

    /*
     * Misc getters / setters
     */

    /**
     * Returns current canonicalization context
     *
     * @return Sisyphus_C14n_Context
     */
    public function getContext()
    {
        return $this->_context;
    }

    /**
     * Updates current canonicalization context
     *
     * @param Sisyphus_C14n_Context $value
     * @return self
     */
    public function setContext($value)
    {
        $this->_context = $value;
        return $this;
    }

    /**
     * Returns current document subset
     *
     * @return Sisyphus_C14N_Legacy_Nodeset
     */
    protected function getNodeset()
    {
        return $this->_nodeset;
    }

    /**
     * Updates current document subset
     *
     * @param Sisyphus_C14N_Legacy_Nodeset $value
     * @return self
     */
    protected function setNodeset($value)
    {
        $this->_nodeset = $value;
        return $this;
    }
}
