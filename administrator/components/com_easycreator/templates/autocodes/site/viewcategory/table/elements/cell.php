<?php
/**
 * @version $Id: cell.php 406 2011-05-31 17:59:02Z elkuku $
 * @package		EasyCreator
 * @subpackage	AutoCodes
 * @author		Nikolai Plath (elkuku) {@link http://www.nik-it.de NiK-IT.de}
 * @author		Created on 07-Mar-2010
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

/**
 * Enter description here ...@todo class doccomment.
 *
 */
class AutoCodeSiteViewCategoryTableElementCell
{
    /**
     * Gets the HTML code.
     *
     * @param EasyTable $table A EasyTable object
     * @param string $indent Indentation string
     *
     * @return string HTML
     */
    public function getCode(EasyTable $table, $indent = '')
    {
        $ret = '';

        foreach($table->getFields() as $field)
        {
            if( ! $field->display)
            {
                continue;
            }

            $ret .= $indent.'<td>'.NL;
            $ret .= $indent.'    <?php echo $item->'.$field->name.'; ?>'.NL;
            $ret .= $indent.'</td>'.NL;
        }//foreach

        return $ret;
    }//function
}//class
