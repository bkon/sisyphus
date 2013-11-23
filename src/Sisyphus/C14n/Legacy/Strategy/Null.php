<?php
/**
 * "Fallback" canonicalization strategy.
 *
 * @author Konstantin Burnaev <kbourn@gmail.com>
 * @copyright Copyright (c) 2013, Konstantin Burnaev
 * @license http://opensource.org/licenses/MIT MIT
 */

/**
 * Implements  "catch-all"  canonicalization  strategy for  nodes  not
 * generating any canonicalized output.
 */
class Sisyphus_C14n_Legacy_Strategy_Null
    implements Sisyphus_C14n_Legacy_Strategy_StrategyInterface
{
    /**
     * Fallback  canonicalization  for  nodes  with  no  canonicalized
     * presentation
     *
     * @param mixed $node
     *
     * @return string empty string
     */
    public function canonicalize($node)
    {
        return '';
    }
}
