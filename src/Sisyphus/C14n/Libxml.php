<?php
/**
 * Libxml-based canonicalization
 *
 * @author Konstantin Burnaev <kbourn@gmail.com>
 * @copyright Copyright (c) 2013, Konstantin Burnaev
 * @license http://opensource.org/licenses/MIT MIT
 */

/**
 * Canonicalization service implementation based on built-in libxml
 * C14N method (available since PHP 5.2)
 */
class Sisyphus_C14n_Libxml
    extends Sisyphus_C14n_C14nAbstract
{
    /**
     * Canonicalizes XML node.
     *
     * @see Sisyphus_C14n_C14nInterface::canonicalize()
     * Sisyphus_C14n_C14nInterface::canonicalize()
     *
     * @link http://php.net/manual/en/domnode.c14n.php DOMNode::C14N()
     *
     * @param DOMNode $xml DOM node to canonicalize
     *
     * @return string canonicalized XML as string
     */
    public function canonicalize(
        DOMNode $xml)
    {
        $inclusiveNamespaces = $this->getContext()->isExclusive() ?
            $this->getContext()->getInclusiveNamespaces() :
            null;

        return $xml->C14N(
            $this->getContext()->isExclusive(),
            $this->getContext()->isWithComments(),
            $this->prepareXpathData(),
            $inclusiveNamespaces
        );
    }

    /*
     * Implementation details
     */

    /**
     * Prepares data for the third parameter of the DOMNode::C14N call.
     *
     * $xpath parameter of DOMNode::C14N method accepts an array with
     * following elements:
     *
     * - query (string) - contains XPath expression for the document
     *   subset
     *
     * - namespaces (string[]) - contains namespace prefix to
     *   namespace url mapping. This list of namespaces is defined
     *   when XPath expression is evaluated.
     *
     * This  data makes  sense  only if  we're  working with  document
     * subset;  otherwise we  should  just pass  null  as a  parameter
     * value.
     *
     * @link http://php.net/manual/en/domnode.c14n.php DOMNode::C14N()
     *
     * @return null|mixed[] data for the C14N call
     */
    protected function prepareXpathData()
    {
        if (!$this->getContext()->getQuery()) {
            // No document subset defined
            return null;
        };

        return array(
            'query' => $this->getContext()->getQuery(),
            'namespaces' => $this->getContext()->getNamespaces()
        );
    }
}