<?php
/**
 * PHP-based canonicalization
 *
 * @author Konstantin Burnaev <kbourn@gmail.com>
 * @copyright Copyright (c) 2013, Konstantin Burnaev
 * @license http://opensource.org/licenses/MIT MIT
 */

/**
 * Pure-PHP canonicalization service implementation (PHP 5.1.x)
 */
class Sisyphus_C14n_Legacy
    extends Sisyphus_C14n_C14nAbstract
{
    /**
     * @var DOMNode[]|null list of nodes included in the document
     * subset or null if document is being processed as a whole
     */
    private $_nodeset;

    /**
     * Canonicalizes XML node.
     *
     * @see Sisyphus_C14n_C14nInterface::canonicalize()
     * Sisyphus_C14n_C14nInterface::canonicalize()
     *
     * @param DOMNode $xml DOM node to canonicalize
     *
     * @return string canonicalized XML as string
     */
    public function canonicalize(
        DOMNode $xml)
    {
        $this->setNodeset(
            new Sisyphus_C14n_Legacy_Nodeset(
                $xml instanceof DOMDocument ? $xml : $xml->ownerDocument,
                $xml,
                $this->getContext()
            )
        );

        $strategy = new Sisyphus_C14n_Legacy_Strategy_Dispatcher(
            $this->getContext(),
            $this->getNodeset()
        );

        return $strategy->canonicalize($this->getNodeset()->getApex());
    }

    /**
     * Implenentation details
     */

    /*
     * Getters / setters
     */

    /**
     * Returns current document subset
     *
     * @return DOMNodeList current document subset
     */
    protected function getNodeset()
    {
        return $this->_nodeset;
    }

    /**
     * Replaces current document subset
     *
     * @param DOMNodeList $value
     *
     * @return self
     */
    protected function setNodeset($value)
    {
        $this->_nodeset = $value;
        return $this;
    }
}