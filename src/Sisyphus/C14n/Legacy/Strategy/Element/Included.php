<?php
/**
 * DOM Element canonicalization strategy implementation
 *
 * @author Konstantin Burnaev <kbourn@gmail.com>
 * @copyright Copyright (c) 2013, Konstantin Burnaev
 * @license http://opensource.org/licenses/MIT MIT
 */

/**
 * Implements element canonicalization  strategy for elements included
 * in the document subset.
 */
class Sisyphus_C14n_Legacy_Strategy_Element_Included
    extends Sisyphus_C14n_Legacy_Strategy_Element_ElementAbstract
{
    /**
     * Generates canonical string presentation of an XML Element included
     * in the document subset
     *
     * @see  Sisyphus_C14n_Legacy_Strategy_Element::canonicalize()
     * the description of the canonicalization algorithm
     *
     * @param string $nodeName XML node name
     *
     * @param string $namespaceString string presentation of the
     * namespace axis
     *
     * @param string $attributeString string presentation of the
     * attribute axis
     *
     * @param   string  $nestedString   string  presentation   of  the
     * descendant axis
     *
     * @return string canonical presentation of the current node generated from
     * the above parts.
     */
    protected function generateOutputString(
        $nodeName,
        $namespaceString,
        $attributeString,
        $nestedString)
    {
        return sprintf(
            '<%s%s%s>%s</%s>',
            $nodeName,
            $namespaceString,
            $attributeString,
            $nestedString,
            $nodeName
        );
    }
}
