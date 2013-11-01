<?php
/**
 * @version SVN: $Id: phpcpd.php 30 2010-11-09 23:54:27Z elkuku $
 * @package    EasyCreator
 * @subpackage Helpers
 * @author		Nikolai Plath {@link http://www.nik-it.de}
 * @author		Created on 28-Sep-2009
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

ecrLoadHelper('pearhelpers.consolehelper');

/**
 * PHP Copy & Paste detector helper.
 *
 * @package EasyCreator
 */
class EasyPHPCPD extends EasyPearConsole
{
    /**
     * Runs the duplicated code detection.
     *
     * @param array $arguments Indexed array with arguments.
     * @param mixed $dirs One or more directories.
     *
     * @return string HTML (?)
     */
    public function detect($arguments, $dirs)
    {
        if(ECR_DEBUG)
        var_dump($dirs);

        if(is_array($dirs))
        {
//            ###    	$dir = JPATH_ROOT.DS.$dirs[1];
            //@todo - when phpcpd supports multiple dirs..
        }
        else
        {
            /*
             * Parse directories
             * clean path
             */
            $dir = JPATH_ROOT.DS.str_replace('/', DS, $dirs);
        }

        $args = array();

        if(count($arguments))
        {
            foreach($arguments as $name => $value)
            {
                $args[] = '--'.$name.' '.$value;
            }//foreach
        }

        $args[] = $dir;

        $results = $this->cliExec('phpcpd', $args);

        //@todo save to file

        return $results;
    }//function
}//class
