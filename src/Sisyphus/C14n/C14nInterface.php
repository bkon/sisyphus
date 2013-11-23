<?php
/**
 * Canonicalization interface definition.
 *
 * @author Konstantin Burnaev <kbourn@gmail.com>
 * @copyright Copyright (c) 2013, Konstantin Burnaev
 * @license http://opensource.org/licenses/MIT MIT
 */

/**
 * Generic interface of a canonicalization service.
 *
 * Service object is  assumed to be stateful;  it's the responsibility
 * of  the caller  to reset  it to  correct state  if it  is used  for
 * several sequental canonicalizations in a row.
 *
 * Service should retain flags between separate canonicalization
 * calls.
 *
 * Default service state is:
 *
 * - non exclusive canonicalization
 * - whole document is used as a subset
 * - comments are omitted
 * - no predefined namespaces
 * - no inclusive namespaces
 *
 * Sample usage:
 * <pre>
 * $canonicalizer
 *     ->exclusive(true)
 *     ->query($xpath)
 *     ->namespaces(array('ns1' => 'http://example.com'))
 *     ->inclusiveNamespaces(array('ns2'))
 *     ->withComments(false)
 *     ->canonicalize($node);
 * </pre>
 *
 * @link http://www.w3.org/TR/xml-c14n Canonical XML standard
 *
 * @link http://www.w3.org/TR/2002/REC-xml-exc-c14n-20020718/
 * Exclusive XML Canonicalization
 */
interface Sisyphus_C14n_C14nInterface
{
    /**
     * Enables exclusive canonicalization.
     *
     * Keep  in  mind that  using  a  document  subset  is a  part  of
     * Canonical  XML standard,  while Exclusive  XML Canonicalization
     * has a  standard of its  own (and can  be applied to  a document
     * subset too)
     *
     * @param boolean $exclusive flag indicating that we should use
     * exclusive XML canonicalization.
     *
     * @return Sisyphus_C14n_C14nInterface self-reference for call
     * chaining
     *
     * @link http://www.w3.org/TR/2002/REC-xml-exc-c14n-20020718/
     * Exclusive XML Canonicalization
     *
     * @see Sisyphus_C14n_C14nInterface::InclusiveNamespaces() how to
     * define InclusiveNamespaces PrefixList parameter
     */
    function exclusive($exclusive);

    /**
     * Sets  the  inclusive  namespaces  prefix list  to  be  used  in
     * exclusive mode.
     *
     * Sample input:
     * <pre>
     * array(
     *   'ns1',
     *   'ns2'
     * )
     * </pre>
     * 
     * @param string[] $prefixList list of namespace prefixes
     *
     * @return Sisyphus_C14n_C14nInterface  self-reference  for  call
     * chaining
     *
     * @see Sisyphus_C14n_C14nInterface::exclusive() how to enable
     * exclusive mode
     *
     * @link
     * http://www.w3.org/TR/2002/REC-xml-exc-c14n-20020718/#def-InclusiveNamespaces-PrefixList
     * InclusiveNamespaces PrefixList parameter in Exlusive XML
     * Canonicalization
     */
    function inclusiveNamespaces(array $prefixList);

    /**
     * Sets XPath expression for a document subset.
     *
     * Note that  document subsets can  be used in both  Canonical XML
     * and Exclusive XML Canonicalization.
     *
     * Sample input:
     * <pre>
     * (//. | //@* | //namespace::*)
     * [
     *   self::ietf:e1 or (parent::ietf:e1 and not(self::text() or self::e2))
     *   or
     *   count(id("E3")|ancestor-or-self::node()) = count(ancestor-or-self::node())
     * ]
     * </pre>
     *
     * Note that is you use namespace prefixes in the XPath expression
     * (for  example,  'ietf')  you   should  define  them  using  the
     * namespaces() method.
     * 
     * @param string $query XPath expression
     *
     * @return Sisyphus_C14n_C14nInterface self-reference for call
     * chaining
     *
     * @see Sisyphus_C14n_C14nInterface::namespaces() how to define a
     * list of namespace prefixes
     *
     * @link http://www.w3.org/TR/xml-c14n#DocSubsets document subsets
     * in Canonical XML
     */
    function query($query);

    /**
     * Sets the list of namespaces  to define in XPath document subset
     * expression.
     *
     * Let's the this subset XPath expression as an example:
     * <pre>
     * (//. | //@* | //namespace::*)
     * [
     *   self::ietf:e1 or (parent::ietf:e1 and not(self::text() or self::e2))
     *   or
     *   count(id("E3")|ancestor-or-self::node()) = count(ancestor-or-self::node())
     * ]
     * </pre>
     * it utilizes 'ietf'  namespace, so  we need to  provide the  URL of
     * this namespace to XPath engine.
     *
     * Sample input:
     * <pre>
     * array(
     *   'ietf' => 'http://a.example.com',
     *   'ns1' => 'http://b.example.com'
     * )
     * </pre>
     *
     * @param  string[] $namespaces  namespace  prefix  to namespace  URL
     * mapping
     *
     * @return  Sisyphus_C14n_C14nInterface  self-reference  for  call
     * chaining
     *
     * @see Sisyphus_C14n_C14nInterface::query() how to define a
     * document subset
     */
    function namespaces(array $namespaces);

    /**
     * Enables canonical XML with comments
     *
     * @param  boolean  $withComments  flag indicating  that  comments
     * should be included in the canonicalized output
     *
     * @return  Sisyphus_C14n_C14nInterface  self-reference  for  call
     * chaining
     */
    function withComments($withComments);

    /**
     * Canonicalizes XML node.
     *
     * Passing  anything  else  other   than  document  node  has  the
     * following effects:
     *
     * - when no  subset is  defined, an  implicit document  subset is
     * created  including apex  node and  document subtree  defined by
     * this node
     *
     * - when  document subset  is defined,  this  node is  used as  a
     * context  node when  evaluating subset  XPath expression.  It is
     * still possible to include nodes outside this node's substree in
     * the canonicalized output.
     *
     * @param DOMNode $xml DOM node to canonicalize
     *
     * @return string canonicalized XML as string
     */
    function canonicalize(DOMNode $xml);
}