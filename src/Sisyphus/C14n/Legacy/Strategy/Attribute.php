<?php
/**
 * Generic attribute node canonicalization strategy
 *
 * @author Konstantin Burnaev <kbourn@gmail.com>
 * @copyright Copyright (c) 2013, Konstantin Burnaev
 * @license http://opensource.org/licenses/MIT MIT
 */

/**
 * DOM attribute node canonicalization strategy implementation.
 */
class Sisyphus_C14n_Legacy_Strategy_Attribute
    implements Sisyphus_C14n_Legacy_Strategy_StrategyInterface
{
    /**
     * Generates canonical representation of an XML attribute node.
     *
     * <blockquote>Attribute  Nodes- a  space,  the  node's QName,  an
     * equals  sign,  an  open  quotation  mark  (double  quote),  the
     * modified  string  value, and  a  close  quotation mark  (double
     * quote). The string  value of the node is  modified by replacing
     * all ampersands (&) with &amp;, all open angle brackets (<) with
     * &lt;,  all  quotation  mark  characters with  &quot;,  and  the
     * whitespace  characters  #x9,  #xA,   and  #xD,  with  character
     * references. The  character references are written  in uppercase
     * hexadecimal  with  no  leading  zeroes  (for  example,  #xD  is
     * represented by the character reference &#xD;).</blockquote>
     *
     * @link http://www.w3.org/TR/xml-c14n#ProcessingModel Canonical
     * XML Processing Model
     *
     * @param DOMAttr $node document being canonicalized
     *
     * @return string canonicalized string representations
     */
    public function canonicalize($node)
    {
        return sprintf(
            ' %s="%s"',
            $node->nodeName,
            str_replace(
                array('&', '"', '<', "\xD", "\xA", "\x9"),
                array('&amp;', '&quot;', '&lt;', '&#xD;', '&#xA;', '&#x9;'),
                $node->value
            )
        );
    }
}
