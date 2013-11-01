<?php
/**
 * @version SVN: $Id: autocode.php 18 2010-11-08 01:10:19Z elkuku $
 * @package    EasyCreator
 * @subpackage	AutoCodes
 * @author     Nikolai Plath {@link http://www.nik-it.de}
 * @author     Created on 22-Mar-2010
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

/**
 * Enter description here ...@todo class doccomment.
 *
 */
class AutoCodeAdminModelsModel extends EasyAutoCode
{
    protected $tags = array('start' => '/*', 'end' => '*/');

    protected $enclose = true;

    private $indent = '        ';

    /**
     * Get the AutoCode to insert.
     *
     * @param string $type AutoCode type
     * @param EasyTable $table A EasyTable object
     *
     * @return string
     */
    public function getCode($type, EasyTable $table)
    {
        $element = $this->getElement($type, dirname(__FILE__));

        if( ! $element)
        {
            return false;
        }

        return $element->getCode($table, $this->indent);
    }//function
}//class
