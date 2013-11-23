<?php
/**
 * Generic DOM element canonicaization strategy
 *
 * @author Konstantin Burnaev <kbourn@gmail.com>
 * @copyright Copyright (c) 2013, Konstantin Burnaev
 * @license http://opensource.org/licenses/MIT MIT
 */

/**
 * This class implements a dispatcher strategy for DOM Element node
 */
class Sisyphus_C14n_Legacy_Strategy_Element
    implements Sisyphus_C14n_Legacy_Strategy_StrategyInterface
{
    /**
     * Document subset
     *
     * @var Sisyphus_C14n_Legacy_Nodeset
     */
    private $_nodeset;

    /**
     * Canonicalization context
     *
     * @var Sisyphus_C14n_Context
     */
    private $_context;

    /**
     * Initialzes strategy instance
     *
     * @param Sisyphus_C14n_Context $context canonicalization settings
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
     * Generates canonical representation of an XML element
     *
     * <blockquote>  Element  Nodes- If  the  element  is not  in  the
     * node-set,  then  the  result  is  obtained  by  processing  the
     * namespace axis,  then the  attribute axis, then  processing the
     * child  nodes  of the  element  that  are  in the  node-set  (in
     * document order).
     *
     * If the element  is in the node-set, then the  result is an open
     * angle bracket (<), the element  QName, the result of processing
     * the  namespace axis,  the  result of  processing the  attribute
     * axis, a close  angle bracket (>), the result  of processing the
     * child  nodes  of the  element  that  are  in the  node-set  (in
     * document order),  an open angle  bracket, a forward  slash (/),
     * the element QName, and a close angle bracket.</blockquote>
     *
     * <blockquote>Namespace Axis-  Consider a list L  containing only
     * namespace  nodes   in  the   axis  and   in  the   node-set  in
     * lexicographic order (ascending). To  begin processing L, if the
     * first node  is not the default  namespace node (a node  with no
     * namespace  URI  and  no  local name),  then  generate  a  space
     * followed by  xmlns="" if and  only if the  following conditions
     * are met:
     *
     * <ul>
     *
     * <li>the element E that owns the axis is in the node-set</li>
     *
     * <li>The  nearest ancestor  element of  E  in the  node-set has  a
     * default namespace node in the node-set (default namespace nodes
     * always have non-empty values in XPath)</li>
     *
     * </ul>
     *
     * </blockquote>
     *
     * <blockquote>The   latter   condition   eliminates   unnecessary
     * occurrences of xmlns="" in the  canonical form since an element
     * only receives an xmlns="" if its default namespace is empty and
     * if it has an immediate parent  in the canonical form that has a
     * non-empty default  namespace.  To  finish processing  L, simply
     * process every namespace  node in L, except  omit namespace node
     * with  local name  xml, which  defines  the xml  prefix, if  its
     * string                         value                         is
     * http://www.w3.org/XML/1998/namespace.</blockquote>
     *
     * <blockquote>Attribute Axis- In lexicographic order (ascending),
     * process each node  that is in the element's  attribute axis and
     * in the node-set.</blockquote>
     *
     * @link http://www.w3.org/TR/xml-c14n#ProcessingModel Canonical
     * XML Processing Model
     *
     * @param DOMElement $node document being canonicalized
     *
     * @return string canonicalized string representation
     */
    public function canonicalize($node)
    {
        if ($this->getNodeset()->isIncluded($node)) {
            $strategy = new Sisyphus_C14n_Legacy_Strategy_Element_Included(
                $this->getContext(),
                $this->getNodeset()
            );
        } else {
            $strategy = new Sisyphus_C14n_Legacy_Strategy_Element_NotIncluded(
                $this->getContext(),
                $this->getNodeset()
            );
        };

        return $strategy->canonicalize($node);
    }

    /*
     * Getters / setters
     */

    /**
     * Returns canonicalization context
     *
     * @return Sisyphus_C14n_Context
     */
    protected function getContext()
    {
        return $this->_context;
    }

    /**
     * Updates canonicalization context
     *
     * @param Sisyphus_C14n_Context $value
     * @return self
     */
    protected function setContext($value)
    {
        $this->_context = $value;
        return $this;
    }

    /**
     * Returns document subset
     *
     * @return Sisyphus_C14N_Legacy_Nodeset
     */
    protected function getNodeset()
    {
        return $this->_nodeset;
    }

    /**
     * Updates document subset
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
