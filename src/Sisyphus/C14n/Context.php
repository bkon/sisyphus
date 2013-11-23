<?php
/**
 * Canonicalization context definition
 *
 * @author Konstantin Burnaev <kbourn@gmail.com>
 * @copyright Copyright (c) 2013, Konstantin Burnaev
 * @license http://opensource.org/licenses/MIT MIT
 */

/**
 * Canonicalization context implementation.
 *
 * This   class   wraps  all   settings   passed   by  user   to   the
 * canonicalization service and provides convenient access to them
 */
class Sisyphus_C14n_Context
{
    /**
     * @see Sisyphus_C14n_C14nInterface::query() how to set document
     * subset
     *
     * @var string|null XPath expression for the document subset or
     * null if document is processed as a whole
     */
    private $_query = null;

    /**
     * @see Sisyphus_C14n_C14nInterface::exclusive() how to switch
     * between inclusive and exclusive processing.
     *
     * @var boolean flag indicating if document is being processed in
     * exclusive mode.
     */
    private $_exclusive = false;

    /**
     * @see Sisyphus_C14n_C14nInterface::withComments() how to toggle
     * comments mode
     *
     * @var boolean flag indicating if comments should be included in
     * canonicalized output.
     */
    private $_withComments = false;

    /**
     * @see Sisyphus_C14n_C14nInterface::namespaces() how to register
     * namespaces for subset XPath expression.
     *
     * @var string[] namespace prefix to url mapping to be used when
     * generating document subset.
     */
    private $_namespaces = array();

    /**
     * @see Sisyphus_C14n_C14nInterface::namespaces() how to add
     * namespace prefixes to the inclusive namespace list.
     *
     * @var string[] namespace prefixes
     */
    private $_inclusiveNamespaces = array();

    /**
     * Enables exclusive canonicalization.
     *
     * @see Sisyphus_C14n_C14nInterface::exclusive()
     * Sisyphus_C14n_C14nInterface::exclusive()
     *
     * @param boolean $exclusive flag indicating that we should use
     * exclusive XML canonicalization.
     *
     * @return Sisyphus_C14n_C14nAbstract self-reference for call
     * chaining
     */
    public function exclusive($exclusive)
    {
        $this->_exclusive = $exclusive;
        return $this;
    }

    /**
     * Sets  the  inclusive  namespaces  prefix list  to  be  used  in
     * exclusive mode.
     *
     * @see Sisyphus_C14n_C14nInterface::inclusiveNamespaces()
     * Sisyphus_C14n_C14nInterface::inclusiveNamespaces()
     *
     * @param string[] list of namespace prefixes
     *
     * @return Sisyphus_C14n_C14nInterface self-reference for call
     * chaining
     */
    public function inclusiveNamespaces(array $prefixList)
    {
        $this->_inclusiveNamespaces = $prefixList;
        return $this;
    }

    /**
     * Sets XPath expression for a document subset.
     *
     * @see Sisyphus_C14n_C14nInterface::query()
     * Sisyphus_C14n_C14nInterface::query()
     *
     * @param string $query XPath expression
     *
     * @return Sisyphus_C14n_C14nInterface self-reference for call
     * chaining
     */
    public function query($query)
    {
        $this->_query = $query;
        return $this;
    }

    /**
     * Sets the list of namespaces  to define in XPath document subset
     * expression.
     *
     * @see Sisyphus_C14n_C14nInterface::namespaces()
     * Sisyphus_C14n_C14nInterface::namespaces()
     *
     * @param  string[] $namespaces  namespace  prefix  to namespace  URL
     * mapping
     *
     * @return  Sisyphus_C14n_C14nInterface  self-reference  for  call
     * chaining
     */
    public function namespaces(array $namespaces)
    {
        $this->_namespaces = array_merge(
            $this->_namespaces,
            $namespaces
        );

        return $this;
    }

    /**
     * Enables canonical XML with comments
     *
     * @see Sisyphus_C14n_C14nInterface::withComments()
     * Sisyphus_C14n_C14nInterface::withComments()
     *
     * @param  boolean  $withComments  flag indicating  that  comments
     * should be included in the canonicalized output
     *
     * @return  Sisyphus_C14n_C14nInterface  self-reference  for  call
     * chaining
     */
    public function withComments($withComments)
    {
        $this->_withComments = $withComments;
        return $this;
    }

    /**
     * Implementation details
     */

    /**
     * Checks if document should be processed in exlusive or inclusive
     * mode.
     *
     * @see  Sisyphus_C14n_C14nInterface::exclusive()  how  to  switch
     * between exclusive and inclusive modes
     *
     * @return boolean flag indicating that document should be
     * processed in exclusive mode (false - inclusive mode)
     */
    public function isExclusive()
    {
        return $this->_exclusive;
    }

    /**
     * Gets current document subset XPath expression.
     *
     * @see Sisyphus_C14n_C14nInterface::query() how to set document
     * subset XPath expression
     *
     * @return string|null current document  subset expression or null
     * if document should be processed as a whole
     */
    public function getQuery()
    {
        return $this->_query;
    }

    /**
     * Get a list  of namespaces to be registered  for document subset
     * XPath evaluation.
     *
     * @see Sisyphus_C14n_C14nInterface::namespaces() how to register
     * namespaces for document subset expression
     *
     * @return string[] namespace prefix to URL mapping to be
     * registered when document subset is being generated
     */
    public function getNamespaces()
    {
        return $this->_namespaces;
    }

    /**
     * Checks if comments should be included in the canonicalized data.
     *
     * @see Sisyphus_C14n_C14nInterface::withComments() how to toggle
     * comments processing on or off
     *
     * @return boolean flag indicating whether comments should be
     * included in the canonicalized output
     */
    public function isWithComments()
    {
        return $this->_withComments;
    }

    /**
     * Gets  a  list  of  namespaces to  be  processed  as  inclusive.
     * Applicable   only  to   Exlusive  XML   Canonicalization  mode;
     * Canonical XML standard treats all namespaces as inclusive.
     *
     * @see Sisyphus_C14n_C14nInterface::inclusiveNamespaces()  how to
     * add namespace prefixes  to the list of  namespaces processed in
     * inclusive mode
     *
     * @link
     * http://www.w3.org/TR/2002/REC-xml-exc-c14n-20020718/#def-InclusiveNamespaces-PrefixList
     * InclusiveNamespaces PrefixList parameter in Exlusive XML
     * Canonicalization
     *
     * @return string[] list of namespace prefixes
     */
    public function getInclusiveNamespaces()
    {
        return $this->_inclusiveNamespaces;
    }
}