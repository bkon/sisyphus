<?php
/**
 * Document subset.
 *
 * @author Konstantin Burnaev <kbourn@gmail.com>
 * @copyright Copyright (c) 2013, Konstantin Burnaev
 * @license http://opensource.org/licenses/MIT MIT
 */

/**
 * This class contains definition for the document subset to be used by the
 * canonicalization algorithm
 */
class Sisyphus_C14n_Legacy_Nodeset
{
    /**
     * Document  subtree apex  node;  used when  no explicit  document
     * subset is given and canonicalization is started at non-document
     * node.
     *
     * @var DOMNode
     */
    private $_apex = null;

    /**
     * Dist of nodes included in the document subset
     *
     * @var DOMNode[]
     */
    private $_nodeset = null;

    /**
     * Initializes document subset object
     *
     * If  a  query  XPath  expression  is provided,  it  is  used  to
     * determine nodes in the subset; otherwise apex node subtree will
     * be used as a document subset.
     *
     * When query XPath expression is  evaluated, apex node is used as
     * a context.
     *
     * @param DOMDocument $doc document being canonicalized
     *
     * @param DOMNode $apex apex node
     *
     * @param    Sisyphus_C14n_Context   $contenxt    canonicalization
     * settings context
     */
    function __construct(DOMDocument $doc,
        DOMNode $apex,
        Sisyphus_C14n_Context $context)
    {
        $query = $context->getQuery();

        // Reset  apex node  to document  base if  we have  explicitly
        // provided document subset XPath expression
        $this->setApex($query ? $doc : $apex);

        if (!$query) {
            $this->setNodeset(null);
            return $this;
        };

        $xpath = new DOMXpath($doc);

        foreach ($context->getNamespaces() as $prefix => $uri) {
            $xpath->registerNamespace($prefix, $uri);
        };

        $this->setNodeset(
            iterator_to_array(
                $xpath->query(
                    $query,
                    // Note that we should use original apex node as a
                    // context node for XPath expression
                    $apex
                )
            )
        );

        return $this;
    }

    /**
     * Returns  a  closest ancestor  of  the  selected node  which  is
     * visible (included in the document subset)
     *
     * @param DOMElement $node base node
     *
     * @return  DOMElement|null closest  visible ancestor  or null  if
     * none
     */
    public function getVisibleAncestor(DOMElement $node)
    {
        $parent = $node->parentNode;
        while (
            $parent->nodeType == XML_ELEMENT_NODE
            && !$this->isIncluded($parent)
        ) {
            $parent = $parent->parentNode;
        };

        if ($parent->nodeType == XML_ELEMENT_NODE) {
            return $parent;
        };

        return null;
    }

    /**
     * Checks if the selected node is included in the document subset.
     *
     * @param DOMNode|DOMNameSpaceNode $node selected node
     *
     * @return boolean  flag indicating that current  node is included
     * in the document subset
     */
    public function isIncluded($node)
    {
        if (!$this->hasNodeset()) {
            return $this->isDescendantOrSelf($node, $this->getApex());
        };

        // Namespace nodes need a workaround
        if ($node->nodeType != XML_NAMESPACE_DECL_NODE) {
            // Use strict comparison - otherwise is_array would attempt to
            // compare string representations and always return true
            return in_array($node, $this->getNodeset(), true);
        };

        foreach ($this->getNodeset() as $comparedNode) {
            if ($comparedNode->nodeType == XML_NAMESPACE_DECL_NODE
                && $comparedNode->nodeName == $node->nodeName
                && $comparedNode->nodeValue == $node->nodeValue
                && $comparedNode->parentNode === $node->parentNode) {
                return true;
            };
        };

        return false;
    }

    /*
     * Implementation details
     */

    /**
     * Checks  if a  selected node  is a  member of  a given  document
     * subtree.
     *
     * @param DOMNode|DOMNameSpaceNode $node selected node
     *
     * @param DOMNode $apex root of the document subtree
     *
     * @return boolean flag indicating that selected node is contained
     * in a document subtree defined by the apex node
     */
    protected function isDescendantOrSelf($node, DOMNode $apex)
    {
        if ($node === $apex) {
            return true;
        };

        if (!$node->parentNode) {
            return false;
        };

        return $this->isDescendantOrSelf($node->parentNode, $apex);
    }

    /**
     * Checks if current document subset is defined by XPath expression
     *
     * @return boolean flag - true means that we have XPath query for the
     * document subset
     */
    protected function hasNodeset()
    {
        return !is_null($this->getNodeset());
    }

    /*
     * Getters / setters
     */

    /**
     * Returns current apex node
     *
     * @return DOMNode current apex node.
     */
    public function getApex()
    {
        return $this->_apex;
    }

    /**
     * Updates apex node
     *
     * @param DOMNode $value new apex node
     *
     * @return self
     */
    protected function setApex($value)
    {
        $this->_apex = $value;
        return $this;
    }

    /**
     * Returns a list of nodes in the document subset defined by XPath
     * expression.   Note that  if we  have no  XPath query,  document
     * subset will be defined by the apex node.
     *
     * @return DOMNode[]
     */
    protected function getNodeset()
    {
        return $this->_nodeset;
    }

    /**
     * Changes the list of the node in the document subset
     *
     * @param array $value
     *
     * @return self
     */
    protected function setNodeset($value)
    {
        $this->_nodeset = $value;
        return $this;
    }
}