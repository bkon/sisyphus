<?php
/**
 * Generic comment node canonicalization strategy.
 *
 * @author Konstantin Burnaev <kbourn@gmail.com>
 * @copyright Copyright (c) 2013, Konstantin Burnaev
 * @license http://opensource.org/licenses/MIT MIT
 */

/**
 * Implements canonicalization strategy for DOM comment nodes.
 */
class Sisyphus_C14n_Legacy_Strategy_Comment
    implements Sisyphus_C14n_Legacy_Strategy_StrategyInterface
{
    /**
     * Generates canonical string representation of a comment node.
     *
     * <blockquote>Comment Nodes- Nothing  if generating canonical XML
     * without comments. For canonical XML with comments, generate the
     * opening comment  symbol (<!--), the  string value of  the node,
     * and the closing  comment symbol (-->). Also, a  trailing #xA is
     * rendered after the closing  comment symbol for comment children
     * of the root node with a lesser document order than the document
     * element,  and a  leading  #xA is  rendered  before the  opening
     * comment  symbol of  comment children  of the  root node  with a
     * greater  document order  than  the  document element.  (Comment
     * children of  the root  node represent  comments outside  of the
     * top-level  document element  and outside  of the  document type
     * declaration).  </blockquote>
     *
     * @param DOMComment $node DOM node being canonicalized
     *
     * @return string canonical string representation
     */
    public function canonicalize($node)
    {
        return sprintf(
            '<!--%s-->',
            $node->data
        );
    }
}
