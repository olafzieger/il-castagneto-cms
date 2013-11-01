<?php
/**
 * @version SVN: $Id: adapter.php 469 2011-07-29 19:03:30Z elkuku $
 * @package    EasyCreator
 * @subpackage Helpers
 * @author     Nikolai Plath (elkuku) {@link http://www.nik-it.de NiK-IT.de}
 * @author     Created on 16-Jul-2011
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

/**
 * Database adapter class.
 *
 */
class dbAdapter
{
    protected $nameQuote = '`';

    protected $query = null;

    public function __construct()
    {
    }//function

    public function __get($what)
    {
        if(in_array($what, array()))
        return $this->$what;

        if('queryType' == $what)
        {
            if(isset($this->query->type))
            return $this->query->type;

            return '';
        }

        ecrHTML::displayMessage(get_class($this).' - Undefined property: '.$what, 'error');
    }//function

    public function setQuery($query)
    {
        $q = new stdClass;

        $q->raw = $query;

        $q->type = '';
        $q->processed = $query;

        if(0 == strpos($query, 'CREATE'))//@todo check for a CREATE in adapter
        {
            $q->type = 'create';
            $q->processed = substr($q->raw, 7);
        }

        $this->query = $q;
    }//function

    public function quote($string)
    {
        return $this->nameQuote.$string.$this->nameQuote;
    }//function
}//class
