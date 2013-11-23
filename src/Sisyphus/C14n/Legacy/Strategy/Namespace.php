<?php
/**
 * Namespace node canonicalization strategy
 *
 * @author Konstantin Burnaev <kbourn@gmail.com>
 * @copyright Copyright (c) 2013, Konstantin Burnaev
 * @license http://opensource.org/licenses/MIT MIT
 */

/**
 * Provides namespace node canonicalization strategy
 */
class Sisyphus_C14n_Legacy_Strategy_Namespace
    implements Sisyphus_C14n_Legacy_Strategy_StrategyInterface
{
    const DEFAULT_NAMESPACE_LOCAL_NAME = 'xmlns';

    /**
     * Canonicalization service object
     *
     * @var Sisyphus_C14n_Legacy_Nodeset
     */
    private $_nodeset;

    /**
     * Indicates if we're using Canonical XML or
     * Exclusive XML Canonicalization standard
     *
     * @var boolean
     */
    private $_exclusive = false;

    /**
     * List  of  namespace  prefixes  to  be  processed  as  inclusive
     * namespaces when we use Exclusive XML Canonicalization
     *
     * @var string[]
     */
    private $_inclusiveNamespaces = array();

    /**
     * Initializes the strategy object
     *
     * @param Sisyphus_C14n_Legacy_Nodeset $nodeset document subset
     *
     * @param boolean $exclusive Inclusive / Exlusive mode
     *
     * @param string[] list of inclusive namespace prefixes
     */
    public function __construct(
        Sisyphus_C14n_Legacy_Nodeset $nodeset,
        $exclusive,
        array $inclusiveNamespaces)
    {
        $this->setNodeset($nodeset)
             ->setExclusive($exclusive)
             ->setInclusiveNamespaces($inclusiveNamespaces);
    }

    /**
     * Generates canonical representation of an XML namespace node
     *
     * <blockquote> Namespace Nodes- A namespace  node N is ignored if
     * the nearest ancestor element of  the node's parent element that
     * is in  the node-set has a  namespace node in the  node-set with
     * the  same local  name and  value as  N. Otherwise,  process the
     * namespace node N  in the same way as an  attribute node, except
     * assign the local name xmlns to the default namespace node if it
     * exists (in XPath,  the default namespace node has  an empty URI
     * and local name) </blockquote>
     *
     * <blockquote> A  namespace node  N with a  prefix that  does not
     * appear in the InclusiveNamespaces PrefixList is rendered if all
     * of  the  conditions are  met:  Its  parent  element is  in  the
     * node-set, and it is visibly utilized by its parent element, and
     * the prefix has not yet been rendered by any output ancestor, or
     * the nearest output ancestor of  its parent element that visibly
     * utilizes the namespace prefix does not have a namespace node in
     * the node-set with the same namespace  prefix and value as N.  *
     * </blockquote>
     *
     * @link http://www.w3.org/TR/xml-c14n#ProcessingModel Canonical
     * XML Processing Model
     *
     * @link http://www.w3.org/TR/2002/REC-xml-exc-c14n-20020718/#sec-Specification
     * Specification of Exclusive XML Canonicalization
     *
     * @param DOMNameSpaceNode $node namespace node being canonicalized
     *
     * @return  string canonicalized  string  representation or  empty
     * string if node should not be included in the canonical output
     */
    public function canonicalize($node)
    {
        $uri = $node->nodeValue;
        $prefix = $node->localName;

        if (!$this->isInclusiveNamespace($prefix)) {
            if (!$this->getNodeset()->isIncluded($node->parentNode)) {
                return '';
            };

            if (!$this->nodeVisiblyUtilizesNsPrefix(
                $node->parentNode,
                $prefix
            )) {
                return '';
            };

            $parent = $this->getVisibleAncestorUtilizingPrefix(
                $node->parentNode,
                $prefix
            );

            if ($parent
                && $parent->lookupPrefix($uri) == $prefix) {
                return '';
            };
        } else {
            $parent = $this->getNodeset()
                           ->getVisibleAncestor($node->parentNode);
            if ($parent
                && $parent->lookupPrefix($uri) == $prefix) {
                return '';
            };
        };

        if ($prefix == self::DEFAULT_NAMESPACE_LOCAL_NAME) {
            return sprintf(
                ' xmlns="%s"',
                $uri
            );
        } else {
            return sprintf(
                ' xmlns:%s="%s"',
                $prefix,
                $uri
            );
        };
    }

    /*
     * Implementation details
     */

    /**
     * Checks if selected namespace should be processed in exclusive or
     * inclusive mode
     *
     * @param string $prefix namespace prefix value
     *
     * @return boolean flag indicating that this namespace should be precessed
     * in inclusive mode
     */
    protected function isInclusiveNamespace($prefix)
    {
        if (!$this->isExclusive()) {
            return true;
        };

        return in_array(
            $prefix,
            $this->getInclusiveNamespaces()
        );
    }

    /**
     * Checks if a selected DOM node visibly utilized namespace prefix.
     *
     * <blockquote> An element E in a document subset visibly utilizes
     * a namespace  declaration, i.e. a  namespace prefix P  and bound
     * value V, if E or an  attribute node in the document subset with
     * parent  E has  a qualified  name in  which P  is the  namespace
     * prefix.  A  similar definition  applies for an  element E  in a
     * document  subset that  visibly utilizes  the default  namespace
     * declaration,   which    occurs   if   E   has    no   namespace
     * prefix.</blockquote>
     *
     * @link
     * http://www.w3.org/TR/2002/REC-xml-exc-c14n-20020718/#sec-Terminology
     * definition of the "visible utilizes" in Exclusive XML
     * Canonicalization
     *
     * @param DOMElement $node selected DOM node
     *
     * @param string $prefix namespace prefix
     *
     * @return  boolean flag  indicating that  this prefix  is visibly
     * utilized by this node
     */
    protected function nodeVisiblyUtilizesNsPrefix(DOMElement $node, $prefix)
    {
        if ($node->prefix == $prefix) {
            return true;
        };

        foreach ($node->attributes as $attr) {
            if ($attr->prefix == $prefix) {
                return true;
            };
        };

        return false;
    }

    /**
     * Returns an ancestor of the selected node (if any) which visibly
     * utilizes given namespace prefix
     *
     * @link
     * http://www.w3.org/TR/2002/REC-xml-exc-c14n-20020718/#sec-Terminology
     * definition of the "visible utilizes" in Exclusive XML
     * Canonicalization
     *
     * @param DOMElement $node selected DOM node
     *
     * @param string $prefix namespace prefix
     *
     * @return DOMElement|null ancestor or null if none
     */
    protected function getVisibleAncestorUtilizingPrefix(
        DOMElement $node,
        $prefix)
    {
        $parent = $node->parentNode;
        while (
            $parent->nodeType == XML_ELEMENT_NODE
            && (
                !$this->getNodeset()->isIncluded($parent)
                || !$this->nodeVisiblyUtilizesNsPrefix($parent, $prefix)
            )
        ) {
            $parent = $parent->parentNode;
        };

        if ($parent->nodeType != XML_ELEMENT_NODE) {
            return null;
        };

        return $parent;
    }

    /*
     * Misc getters / setters
     */

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

    /**
     * Returns flag indicating we're running in exclusive mode
     *
     * @return boolean
     */
    public function isExclusive()
    {
        return $this->_exclusive;
    }

    /**
     * Updates exlcusive mode flag
     *
     * @param boolean $value
     * @return self
     */
    public function setExclusive($value)
    {
        $this->_exclusive = $value;
        return $this;
    }

    /**
     * Returns  a  list  of  namespace prefixes  to  be  processed  in
     * inclusive mode.
     *
     * @return string[]
     */
    public function getInclusiveNamespaces()
    {
        return $this->_inclusiveNamespaces;
    }

    /**
     * Updates a list of namespaces to be processed in inclusive mode.
     *
     * @param string[] $value
     * @return self
     */
    public function setInclusiveNamespaces($value)
    {
        $this->_inclusiveNamespaces = $value;
        return $this;
    }
}
