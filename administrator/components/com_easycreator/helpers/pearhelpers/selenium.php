<?php
/**
 * @version SVN: $Id: selenium.php 30 2010-11-09 23:54:27Z elkuku $
 * @package    EasyCreator
 * @subpackage Helpers
 * @author     Nikolai Plath {@link http://www.nik-it.de}
 * @author     Created on 28-May-2010
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

ecrLoadHelper('pearhelpers.consolehelper');

/**
 * Selenium test Helper.
 *
 * @package    EasyCreator
 */
class EasySeleniumTest extends EasyPearConsole
{
    public $logPath = '';

    /**
     * Run a selenium test.
     *
     * @param array $arguments Arguments to pass
     *
     * @return string
     */
    public function test($arguments = array())
    {
        if( ! count($arguments))
        {
            return '';
        }

        $results = $this->cliExec('phpunit', $arguments);

        return $results;
    }//function
}//class
