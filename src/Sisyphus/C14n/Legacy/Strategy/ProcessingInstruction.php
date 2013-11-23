<?php
/**
 * Generic processing instruction node canonicalization strategy
 *
 * @author Konstantin Burnaev <kbourn@gmail.com>
 * @copyright Copyright (c) 2013, Konstantin Burnaev
 * @license http://opensource.org/licenses/MIT MIT
 */

/**
 * PI node canonicalization strategy
 */
class Sisyphus_C14n_Legacy_Strategy_ProcessingInstruction
    implements Sisyphus_C14n_Legacy_Strategy_StrategyInterface
{
    /**
     * Generates canonicalized string representation of a processing
     * instruction node.
     *
     * <blockquote>Processing Instruction  (PI) Nodes- The  opening PI
     * symbol (<?),  the PI target name  of the node, a  leading space
     * and the  string value if  it is not  empty, and the  closing PI
     * symbol (?>).   If the string  value is empty, then  the leading
     * space is not added. Also, a  trailing #xA is rendered after the
     * closing  PI symbol  for PI  children of  the root  node with  a
     * lesser document order than the  document element, and a leading
     * #xA is rendered before the opening  PI symbol of PI children of
     * the root node  with a greater document order  than the document
     * element.</blockquote>
     *
     * @param   DOMProcessingInstruction    $node   PI    node   being
     * canonicalized
     *
     * @return string canonicalized string representation
     */
    public function canonicalize($node)
    {
        if ($node->data) {
            return sprintf(
                '<?%s %s?>',
                $node->target,
                $node->data
            );
        } else {
            return sprintf(
                '<?%s?>',
                $node->target,
                $node->data
            );
        };
    }
}
