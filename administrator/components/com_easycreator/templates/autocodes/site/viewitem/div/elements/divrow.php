<?php
/**
 * @version $Id: divrow.php 18 2010-11-08 01:10:19Z elkuku $
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
class AutoCodeSiteViewitemDivElementDivrow
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

            $ret .= $indent.'<div class="title">'.$field->label.'</div>'.NL;
            $ret .= $indent.'<div class="cell"><?php echo $row->'.$field->name.'; ?></div>'.NL;
        }//foreach

        return $ret;
    }//function
}//class
