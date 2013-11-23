<?php
/**
 * Base canonicalization service functionality.
 *
 * @author Konstantin Burnaev <kbourn@gmail.com>
 * @copyright Copyright (c) 2013, Konstantin Burnaev
 * @license http://opensource.org/licenses/MIT MIT
 */

/**
 * Common functionality for canonicalization services.
 */
abstract class Sisyphus_C14n_C14nAbstract
    implements Sisyphus_C14n_C14nInterface
{
    /**
     * Contains details about the canonicalization mode
     *
     * @var Sisyphus_C14n_Context
     */
    private $_context;

    /**
     * Prepares generic parts of the canonicalization service
     */
    public function __construct()
    {
        $this->_context = new Sisyphus_C14n_Context();
    }

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
        $this->getContext()->exclusive($exclusive);
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
        $this->getContext()->inclusiveNamespaces($prefixList);
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
        $this->getContext()->query($query);
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
        $this->getContext()->namespaces($namespaces);
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
        $this->getContext()->withComments($withComments);
        return $this;
    }

    /**
     * Getters / setters
     */

    /**
     * Returns current canonicalization settings object
     *
     * @return Sisyphus_C14n_Context
     */
    protected function getContext()
    {
        return $this->_context;
    }
}