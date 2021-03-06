<?php
/**
 * @version SVN: $Id: EasyPart.php 18 2010-11-08 01:10:19Z elkuku $
 * @package    EasyCreator
 * @subpackage Helpers
 * @author     Nikolai Plath {@link http://www.nik-it.de}
 * @author     Created on 16-Sep-2009
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

/**
 * Base class for EasyParts.
 *
 */
class EasyPart extends JObject
{
    public $group = '';

    public $name = '';

    public $key = '';

    public $basePath = '';

    private $_element = '';

    private $_scope = '';

    private $_name = '';

    /**
     * Constructor.
     *
     * @param string $group Group name
     * @param string $name Part name
     * @param string $element Element name
     * @param string $scope Scope name
     */
    public function __construct($group, $name, $element, $scope)
    {
        $this->key = "$group.$name.$element.$scope";
    }//function

    /**
     * Get the parts name.
     *
     * @return string
     */
    private function getName()
    {
        return $this->group.'.'.$this->name.'.'.$this->_element.'.'.$this->_scope;
    }//function
}//class
