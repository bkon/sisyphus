<?php
/**
 * Generic canonicanization strategy
 *
 * @author Konstantin Burnaev <kbourn@gmail.com>
 * @copyright Copyright (c) 2013, Konstantin Burnaev
 * @license http://opensource.org/licenses/MIT MIT
 */

/**
 * Interface for a generic canonicalization strategy
 */
interface Sisyphus_C14n_Legacy_Strategy_StrategyInterface
{
    /**
     * Generates canonical XML string for a generic node
     *
     * Unfortunately, DOMNameSpaceNode is not inherited from anything,
     * so we  don't have a common  superclass for nodes to  use in the
     * type hint
     *
     * @param DOMNode|DOMNameSpaceNode $node node being canonicalized
     *
     * @return string
     */
    function canonicalize($node);
}