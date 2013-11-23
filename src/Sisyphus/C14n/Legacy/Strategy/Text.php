<?php
/**
 * Generic text node canonicalization strategy
 *
 * @author Konstantin Burnaev <kbourn@gmail.com>
 * @copyright Copyright (c) 2013, Konstantin Burnaev
 * @license http://opensource.org/licenses/MIT MIT
 */

/**
 * Text / CDATA node canonicalization strategy
 */
class Sisyphus_C14n_Legacy_Strategy_Text
    implements Sisyphus_C14n_Legacy_Strategy_StrategyInterface
{
    /**
     * Generates canonical representation of a text node
     *
     * <blockquote>  Text   Nodes-  the   string  value,   except  all
     * ampersands are replaced  by &amp;, all open  angle brackets (<)
     * are  replaced  by &lt;,  all  closing  angle brackets  (>)  are
     * replaced by &gt;, and all #xD characters are replaced by &#xD;.
     * </blockquote>
     *
     * @link http://www.w3.org/TR/xml-c14n#ProcessingModel Canonical
     * XML Processing Model
     *
     * @param DOMText $node node being canonicalized
     *
     * @return string canonicalized string representation
     */
    public function canonicalize($node)
    {
        return str_replace(
            array('&', '>', '<', "\xD"),
            array('&amp;', '&gt;', '&lt;', '&#xD;'),
            $node->data
        );
    }
}
