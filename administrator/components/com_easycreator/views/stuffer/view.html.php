<?php
/**
 * @version SVN: $Id: view.html.php 454 2011-07-23 03:39:22Z elkuku $
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath (elkuku) {@link http://www.nik-it.de NiK-IT.de}
 * @author     Created on 07-Mar-2008
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
class EasyCreatorViewStuffer extends JView
{
    protected $plumbing = true;

    /**
     * Standard display method.
     *
     * @param string $tpl The name of the template file to parse;
     *
     * @return void
     */
    public function display($tpl = null)
    {
        ecrScript('stuffer');

        $this->ecr_project = JRequest::getCmd('ecr_project');

        //--Get the project
        try
        {
            $this->project = EasyProjectHelper::getProject();

            if('package' == $this->project->type
            && ! $this->project->creationDate)
            {
                //-- This is a hack to detect that a package has no install manifest :(
                throw new Exception(jgettext('Invalid project'));
            }
        }
        catch(Exception $e)
        {
            $m =(JDEBUG || ECR_DEBUG) ? nl2br($e) : $e->getMessage();

            ecrHTML::displayMessage($m, 'error');

            ecrHTML::easyFormEnd();

            return;
        }//try

        $task = JRequest::getCmd('task', 'stuffer');
        $tmpl = JRequest::getCmd('tmpl');

        if($task != 'display_snip'
        && $task != 'aj_reflection'
        && $tmpl != 'component')
        {
            //--Draw h1 header
            ecrHTML::header(jgettext('Configure'), $this->project, 'ecr_config');

            //--Draw the submenu if task is not for a raw view
            echo $this->displayBar($task);
        }

        if(in_array($task, get_class_methods($this)))
        {
            //--Execute the task
            $this->$task();

            if($task == 'display_snip')
            {
                //--Raw view
                parent::display($tpl);

                return;
            }
        }
        else
        {
            if($task)
            echo 'UNDEFINED..'.$task.'<br />';

            $this->stuffer();
        }

        $this->assignRef('task', $task);
        $this->assignRef('tmpl', $tmpl);

        parent::display($tpl);

        ecrHTML::easyFormEnd();
    }//function

    /**
     * Displays the submenu.
     *
     * @param string $task The actual task
     *
     * @return string html
     */
    private function displayBar($task)
    {
        $ecr_help = JComponentHelper::getParams('com_easycreator')->get('ecr_help');

        $subtasks = array(
        array('title' => jgettext('Building')
        , 'description' => jgettext('Shows options for building your project like credits, files and folders to copy, languages and admin menu.')
        , 'icon' => 'ecr_config'
        , 'task' => 'stuffer'
        )
        , array('title' => jgettext('Files')
        , 'description' => jgettext('Shows all the files belonging to your project')
        , 'icon' => 'directory'
        , 'task' => 'files'
        )
        , array('title' => jgettext('Installing')
        , 'description' => jgettext('Create and modify install and uninstall files for your project.')
        , 'icon' => 'install'
        , 'task' => 'install'
        )
        , array('title' => jgettext('Parameters')
        , 'description' => jgettext('Modify your project parameters stored in XML files.')
        , 'icon' => 'ecr_params'
        , 'task' => 'projectparams'
        )
        , array('title' => jgettext('DataDesigner')
        , 'description' => jgettext('Automated source code and db creation for your project.')
        , 'icon' => 'ecr_db'
        , 'task' => 'tables'
        )
        , array('title' => jgettext('Remove Project')
        , 'description' => jgettext('This will your delete your project.')
        , 'icon' => 'delete'
        , 'task' => 'projectdelete'
        )
        );

        $htmlDescriptionDivs = '';
        $jsVars = '';
        $jsMorphs = '';
        $jsEvents = '';
        $html = '';

        if($task == 'stuffer')
        {
            $html .= '<div class="ecr_easy_toolbar" style="float: right;">';
            $html .= '	<div class="ecr_button img icon-16-save" onclick="submitStuffer(\'save_config\');">'
            .jgettext('Save').'</div>';
            $html .= '</div>';
        }

        $html .= '<div id="ecr_sub_toolbar" style="margin-bottom: 1em; margin-top: 0.5em;">';

        foreach($subtasks as $sTask)
        {
            if($this->project->type != 'component'
            && ($sTask['task'] == 'tables'
            || $sTask['task'] == 'install')
            )
            {
                continue;
            }

            if($this->project->type == 'package')
            {
                if($sTask['task'] != 'stuffer'
                && $sTask['task'] != 'projectdelete')
                continue;
            }

            $selected =($sTask['task'] == $task) ? '_selected' : '';
            $html .= '<span id="btn_'.$sTask['task'].'" style="margin-left: 0.3em;"';
            $html .= ' class="ecr_button'.$selected.' img icon-16-'.$sTask['icon'].'"';
            $html .= ' onclick="submitbutton(\''.$sTask['task'].'\');">';
            $html .= $sTask['title'].'</span>';

            if($ecr_help == 'all'
            || $ecr_help == 'some')
            {
                $htmlDescriptionDivs .= '<div class="hidden_div ecr_description" id="desc_'.$sTask['task'].'">'
                .$sTask['description'].'</div>';
                $jsVars .= "var desc_".$sTask['task']." = $('desc_".$sTask['task']."');\n";

                $jsEvents .= "$('btn_".$sTask['task']."').addEvents({\n"
                . "'mouseenter': showTaskDesc.bind(desc_".$sTask['task']."),\n"
                . "'mouseleave': hideTaskDesc.bind(desc_".$sTask['task'].")\n"
                . "});\n";
            }
        }//foreach

        $html .= $htmlDescriptionDivs;

        if($ecr_help == 'all'
        || $ecr_help == 'some')
        {
            $html .= "<script type='text/javascript'>"
            ."window.addEvent('domready', function() {\n"
            ."function showTaskDesc(name) {\n"
            ."this.setStyle('display', 'block');\n"
            ."}\n"
            ."function hideTaskDesc(name) {\n"
            ."	this.setStyle('display', 'none');\n"
            ."}\n"
            . $jsVars
            . $jsEvents
            . "});\n"
            . "</script>";
        }

        $html .= '</div>';

        return $html;
    }//function

    /**
     * Stuffer View.
     *
     * @return void
     */
    private function stuffer()
    {
        $this->projectList = EasyProjectHelper::getProjectList();
        $this->installFiles = EasyProjectHelper::findInstallFiles($this->project);

        $this->setLayout('stuffer');
    }//function

    private function new_element()
    {
        $this->files();
    }//function

    /**
     * Files View.
     *
     * @return void
     */
    private function files()
    {
        ecrLoadHelper('file');
        $this->easyFile = new EasyFile;

        ecrScript('addelement');
        $this->setLayout('files');
    }//function

    /**
     * Install View.
     *
     * @return void
     */
    private function install()
    {
        $this->installFiles = EasyProjectHelper::findInstallFiles($this->project);

        $this->setLayout('install');
    }//function

    /**
     * Delete project View.
     *
     * @return void
     */
    private function projectdelete()
    {
        $this->setLayout('deleteconfirm');
    }//function

    /**
     * Project parameters View.
     *
     * @return void
     */
    private function projectparams()
    {
        $selected_xml = JRequest::getVar('selected_xml');
        $params = array();
        $xmlFiles = array();

        foreach($this->project->copies as $path)
        {
            if(JFolder::exists($path))
            {
                $files = JFolder::files($path, '\.xml', true, true);

                if(count($files))
                {
                    foreach($files as $file)
                    {
                        $xmlFiles[] = substr($file, strlen(JPATH_ROOT) + 1);
                    }//foreach
                }
            }
            else if(JFile::getExt($path) == 'xml')
            {
                $xmlFiles[] = substr($path, strlen(JPATH_ROOT) + 1);
            }
        }//foreach

        if(in_array($selected_xml, $xmlFiles))
        {
            switch(ECR_JVERSION)
            {
                case '1.5':
                    $this->params = new JParameter('', JPATH_ROOT.DS.$selected_xml);
                    break;

                case '1.6':
                case '1.7':
                    $this->params = JFactory::getXML(JPATH_ROOT.DS.$selected_xml);
                    break;

                default:
                    ecrHTML::displayMessage(__METHOD__.' - Undefined J! version', 'error');

                    return false;
                    break;
            }//switch
            //--Get the project params
        }

        $options = array();
        $options[] = JHTML::_('select.option', '', jgettext('Select'));

        for($i = 1; $i < count($xmlFiles) + 1; $i++)
        {
            $options[$i] = JHTML::_('select.option', $xmlFiles[$i - 1]);
        }//for

        $xmlSelector = JHTML::_('select.genericlist', $options, 'selected_xml'
        , 'style="font-size: 1.3em;" onchange="submitbutton(\''.JRequest::getCmd('task').'\');"'
        , 'value', 'text', $selected_xml);
        $this->assignRef('xmlSelector', $xmlSelector);

        $this->assignRef('selected_xml', $selected_xml);

        $layout = 'projectparams';

        if('1.5' == ECR_JVERSION)
        $layout .= '_15';

        $this->setLayout($layout);
    }//function

    /**
     * Table edit View.
     *
     * @return void
     */
    private function table_edit()
    {
        $this->plumbing = false;
        $this->tables();
    }//function

    /**
     * Tables View.
     *
     * @return void
     */
    private function tables()
    {
        ecrScript('addelement');

        $this->setLayout('tables');
    }//function

    /**
     * Register table View.
     *
     * @return void
     */
    private function register_table()
    {
        $this->tables();
    }//function

    /**
     * Create table View.
     *
     * @return void
     */
    private function createTable()
    {
        $this->setLayout('tables');
    }//function

    /**
     * Display snippet View.
     *
     * @return void
     */
    private function display_snip()
    {
        $path = JRequest::getVar('file_path');
        $start = JRequest::getInt('start');
        $end = JRequest::getInt('end');

        if( ! JFile::exists($path))
        {
            echo '<div class="ebc_error" align="center">'.jgettext('File not found').'</div>';
            echo $path;

            //--EXIT
            jexit();
        }
        else
        {
            $fileContents = JFile::read($path);
        }

        if($fileContents)
        {
            $fileContents = explode("\n", $fileContents);

            $this->assignRef('fileContents', $fileContents);
            $this->assignRef('startAtLine', $start);
            $this->assignRef('endAtLine', $end);
            $this->assignRef('path', $path);
        }

        $this->setLayout('snippet');
    }//function

    /**
     * draws a list of related links
     */
    public function drawDocLinks()
    {
        $docLinks = array(
        'Standard parameter types' => 'http://docs.joomla.org/Standard_parameter_types'
        , 'Reference: XML parameters'
        => 'http://dev.joomla.org/component/option,com_jd-wiki/Itemid,/id,references:xml_parameters/'
        , 'Component parameters' => 'http://docs.joomla.org/Component_parameters'
        , 'Defining a parameter in templateDetails.xml'
        => 'http://docs.joomla.org/Defining_a_parameter_in_templateDetails.xml'
        , 'Creating custom template parameter types'
        => 'http://docs.joomla.org/Creating_custom_template_parameter_types'
        );

        $ret = '';

        $ret .= '<br /><hr /><br />';
        $ret .= '<div class="explanation">';
        $ret .= '<br /><strong style="background-color: white; padding: 5px;">'.jgettext('Infos on parameters (external)').'</strong>';
        $ret .= '<ul>';

        foreach($docLinks as $title => $link)
        {
            $ret .= '<li><a class="external" href="'.$link.'" target="_blank" />'.$title.'</a></li>';
        }//foreach
        $ret .= '</ul>';
        $ret .= '</div>';
        $ret .= '<br />';

        return $ret;
    }//function
}//class
