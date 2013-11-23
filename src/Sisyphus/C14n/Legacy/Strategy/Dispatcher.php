<?php
/**
 * Generic canonicalization strategy for a generic DOM node.
 *
 * @author Konstantin Burnaev <kbourn@gmail.com>
 * @copyright Copyright (c) 2013, Konstantin Burnaev
 * @license http://opensource.org/licenses/MIT MIT
 */

/**
 * Implements  canonicalization  strategy  for  a  generic  DOM  node;
 * determines  the node  type and  delegates the  task for  a concrete
 * strategy implementation.
 */
class Sisyphus_C14n_Legacy_Strategy_Dispatcher
    implements Sisyphus_C14n_Legacy_Strategy_StrategyInterface
{
    /**
     * Current document subset
     *
     * @var Sisyphus_C14n_Legacy_Nodeset
     */
    private $_nodeset;

    /**
     * Canonicalization settings
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
     * Generate canonicalized  string representation of a  generic DOM
     * Node.
     *
     * @param DOMNode $node XML DOM node being canonicalized
     *
     * @return string canonicalized string representation
     */
    public function canonicalize($node)
    {
        switch ($node->nodeType) {
        case XML_DOCUMENT_NODE:
            $strategy = new Sisyphus_C14n_Legacy_Strategy_Document(
                $this->getContext(),
                $this->getNodeset()
            );
            break;

        case XML_ELEMENT_NODE:
            $strategy = new Sisyphus_C14n_Legacy_Strategy_Element(
                $this->getContext(),
                $this->getNodeset()
            );
            break;

        case XML_TEXT_NODE:
        case XML_CDATA_SECTION_NODE:
            $strategy = $this->createTextStrategy($node);
            break;

        case XML_PI_NODE:
            $strategy = $this->createProcessingInstructionStrategy($node);
            break;

        case XML_COMMENT_NODE:
            $strategy = $this->createCommentStrategy();
            break;

        default:
            $strategy = new Sisyphus_C14n_Legacy_Strategy_Null();
            break;
        };

        return $strategy->canonicalize($node);
    }

    /*
     * Implementation details
     */

    /**
     * Returns a concrete canonicalization strategy to be used for
     * a text / CDATA node.
     *
     * @param DOMText|DOMCdata $node node being canonicalized
     *
     * @return Sisyphus_C14n_Legacy_Strategy_StrategyInterface
     * strategy
     */
    protected function createTextStrategy($node)
    {
        if (!$this->getNodeset()->isIncluded($node)) {
            return new Sisyphus_C14n_Legacy_Strategy_Null();
        };

        return new Sisyphus_C14n_Legacy_Strategy_Text();
    }

    /**
     * Returns a concrete  canonicalization strategy to be  used for a
     * PI node.
     *
     * @param DOMProcessingInstruction $node node being canonicalized
     *
     * @return Sisyphus_C14n_Legacy_Strategy_StrategyInterface
     * strategy
     */
    protected function createProcessingInstructionStrategy($node)
    {
        if (!$this->getNodeset()->isIncluded($node)) {
            return new Sisyphus_C14n_Legacy_Strategy_Null();
        };

        return new Sisyphus_C14n_Legacy_Strategy_ProcessingInstruction();
    }

    /**
     * Returns a concrete  canonicalization strategy to be  used for a
     * comment  node. (not  dependent on  the node  - comment-specific
     * strategy is global)
     *
     * @return Sisyphus_C14n_Legacy_Strategy_StrategyInterface
     * strategy
     */
    protected function createCommentStrategy()
    {
        if (!$this->getContext()->isWithComments()) {
            return new Sisyphus_C14n_Legacy_Strategy_Null();
        };

        return new Sisyphus_C14n_Legacy_Strategy_Comment();
    }

    /*
     * Getters / setters
     */

    /**
     * Returns current canonicalization settings
     *
     * @return Sisyphus_C14n_Context
     */
    public function getContext()
    {
        return $this->_context;
    }

    /**
     * Replaces current canonicalization settings object
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
