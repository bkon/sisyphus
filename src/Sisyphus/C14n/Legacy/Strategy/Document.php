<?php
/**
 * Generic XML document canonicalization strategy
 *
 * @author Konstantin Burnaev <kbourn@gmail.com>
 * @copyright Copyright (c) 2013, Konstantin Burnaev
 * @license http://opensource.org/licenses/MIT MIT
 */

/**
 * Implements canonicalization strategy for DOM Document node.
 */
class Sisyphus_C14n_Legacy_Strategy_Document
    implements Sisyphus_C14n_Legacy_Strategy_StrategyInterface
{
    const LINEFEED = "\xA";

    /**
     * Current document subset
     *
     * @var Sisyphus_C14n_Legacy_Nodeset
     */
    private $_nodeset;

    /**
     * Canonicalization settigns object
     *
     * @var Sisyphus_C14n_Context
     */
    private $_context;

    /**
     * Initializes stratery instance object
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
     * Generates canonical representation of an XML document.
     *
     * <blockquote>  Root Node-  The root  node is  the parent  of the
     * top-level document  element.  The result of  processing each of
     * its child nodes that is in  the node-set in document order. The
     * root node does not generate a byte order mark, XML declaration,
     * nor  anything  from  within   the  document  type  declaration.
     * </blockquote>
     *
     * Note: in  addition to  the root XML  element, XML  document can
     * contain other nodes included in the canonical output on the top
     * level (e.g.  processing instructions and comments)
     *
     * @link http://www.w3.org/TR/xml-c14n#ProcessingModel Canonical
     * XML Processing Model
     *
     * @param DOMDocument $node document being canonicalized
     *
     * @return string canonicalized string representation
     */
    public function canonicalize($node)
    {
        $nodeStrings = array();

        foreach ($node->childNodes as $node) {
            $strategy = new Sisyphus_C14n_Legacy_Strategy_Dispatcher(
                $this->getContext(),
                $this->getNodeset()
            );

            $nodeStrings[] = $strategy->canonicalize($node);
        };

        return join(
            self::LINEFEED,
            array_filter($nodeStrings)
        );
    }

    /*
     * Getters / setters
     */

    /**
     * Returns current canocanizliation settings
     *
     * @return Sisyphus_C14n_Context
     */
    protected function getContext()
    {
        return $this->_context;
    }

    /**
     * Replaces canonicalization settings object
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
     * Returns current document subset
     *
     * @return Sisyphus_C14N_Legacy_Nodeset
     */
    protected function getNodeset()
    {
        return $this->_nodeset;
    }

    /**
     * Replaces current document subset
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
