<?php
/**
 * @version SVN: $Id: view.html.php 418 2011-06-13 04:01:43Z elkuku $
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath {@link http://www.nik-it.de}
 * @author     Created on 03-Mar-08
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

jimport('joomla.application.component.view');

/**
 * HTML View class for the EasyCreator Component.
 *
 * @package EasyCreator
 * @subpackage Views
 */
class EasyCreatorViewEasyCreator extends JView
{
    /**
     * Standard display method.
     *
     * @param string $tpl The name of the template file to parse;
     *
     * @return void
     */
    public function display($tpl = null)
    {
        /*
         * Check for evil IE
         * sry - no support for this in penguin land =;)
         */

        jimport('joomla.environment.browser');

        $browser = new JBrowser;

        if($browser->getBrowser() == 'msie')
        {
            JError::raiseWarning(0, jgettext('This extension is not compatible with MS internet explorer R'));
        }

        //-- Check if we are on localhost - otherwise display a warning
        if(JComponentHelper::getParams('com_easycreator')->get('warn_livesite'))
        {
            $ip = getenv('REMOTE_ADDR');

            if($ip && $ip != '127.0.0.1')
            {
                $link = 'http://docs.joomla.org/Setting_up_your_workstation_for_Joomla!_development';
                JError::raiseNotice(100
                , jgettext('Please use this extension only in local development environments.'));
                JError::raiseNotice(100
                , sprintf(
                jgettext("See: <a href=\"%s\">docs.joomla.org/Setting up your workstation for Joomla! development</a>")
                , $link));
                JError::raiseNotice(100
                , jgettext('You may suppress this message in the configuration settings if you\'re shure'));
            }
        }

        parent::display($tpl);

        ecrHTML::easyFormEnd();
    }//function
}//class
