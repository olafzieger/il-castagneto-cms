<?php
/**
 * @version SVN: $Id: template.php 432 2011-06-23 19:19:52Z elkuku $
 * @package    EasyCreator
 * @subpackage ProjectTypes
 * @author     Nikolai Plath {@link http://www.nik-it.de}
 * @author     Created on 24-Mar-2010
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

/**
 * EasyCreator project type template.
 */
class EasyProjectTemplate extends EasyProject
{
    /**
     * Project type.
     *
     * @var string
     */
    public $type = 'template';

    /**
     * Project prefix.
     *
     * @var string
     */
    public $prefix = 'tpl_';

    /**
     * Find all files and folders belonging to the project.
     *
     * @return array
     */
    public function findCopies()
    {
        if($this->copies)
        return $this->copies;

        if($this->scope == 'admin')
        {
            if(JFolder::exists(JPATH_ADMINISTRATOR.DS.'templates'.DS.$this->comName))
            {
                $this->copies[] = JPATH_ADMINISTRATOR.DS.'templates'.DS.$this->comName;
            }
        }
        else
        {
            if(JFolder::exists(JPATH_SITE.DS.'templates'.DS.$this->comName))
            {
                $this->copies[] = JPATH_SITE.DS.'templates'.DS.$this->comName;
            }
        }

        return $this->copies;
    }//function

    /**
     * Gets the language scopes for the extension type.
     *
     * @return array Indexed array.
     */
    public function getLanguageScopes()
    {
        $scopes = array();
        $scopes[] = 'admin';

        if($this->scope != 'admin')
        {
            $scopes[] = 'site';
        }

        return $scopes;
    }//function

    /**
     * Gets the paths to language files.
     *
     * @return array
     */
    public function getLanguagePaths()
    {
        $paths = array();
        $scope =($this->scope == 'admin') ? 'admin' : 'site';
        $paths[$scope] =($this->scope == 'admin') ? JPATH_ADMINISTRATOR : JPATH_SITE;

        return $paths;
    }//function

    /**
     * Get the name for language files.
     *
     * @return string
     */
    public function getLanguageFileName()
    {
        return 'tpl_'.$this->comName.'.ini';
    }//function

    /**
     * Gets the DTD for the extension type.
     *
     * @param string $jVersion Joomla! version
     *
     * @todo DTDs for J! 1.6
     *
     * @return mixed [array index array on success | false if not found]
     */
    public function getDTD($jVersion)
    {
        $dtd = false;

        switch(ECR_JVERSION)
        {
            case '1.5':
                $dtd = array(
                 'type' => 'install'
                 , 'public' => '-//Joomla! 1.5//DTD template 1.0//EN'
                 , 'uri' => 'http://joomla.org/xml/dtd/1.5/template-install.dtd');
                 break;

            case '1.6':
            case '1.7':
                break;

            default:
                break;
        }//switch

        return $dtd;
    }//function

    /**
     * Get a file name for a EasyCreator setup XML file.
     *
     * @return string
     */
    public function getEcrXmlFileName()
    {
        return $this->getFileName().'.xml';
    }//function

    /**
     * Get a common file name.
     *
     * @return string
     */
    public function getFileName()
    {
        return 'tpl_'.$this->scope.'_'.$this->comName;
    }//function

    /**
     * Get the path for the Joomla! XML manifest file.
     *
     * @return string
     */
    public function getJoomlaManifestPath()
    {
        $path =($this->scope == 'admin') ? JPATH_ADMINISTRATOR : JPATH_SITE;
        $path .= DS.'templates'.DS.$this->comName;

        return $path;
    }//function

    /**
     * Get a Joomla! manifest XML file name.
     *
     * @return mixed [string file name | boolean false on error]
     */
    public function getJoomlaManifestName()
    {
        return 'templateDetails.xml';
    }//function

    /**
     * Get the project Id.
     *
     * @return integer Id
     */
    public function getId()
    {
        $db = JFactory::getDbo();

        switch(ECR_JVERSION)
        {
            case '1.5':
                $query = new JDatabaseQuery;

                $query->from('#__components AS c');
                $query->select('c.id');
                $query->where('c.option = '.$db->quote($this->comName));
                $query->where('c.parent = 0');
                break;

            case '1.6':
            case '1.7':
                $query = $db->getQuery(true);

                $query->from('#__extensions AS e');
                $query->select('e.extension_id');
                $query->where('e.element = '.$db->quote($this->comName));
                $query->where('e.type = '.$db->quote('template'));
                break;

            default:
                ecrHTML::displayMessage(__METHOD__.' - Unsupported JVersion');

                return false;
                break;
        }//switch

        $db->setQuery($query);

        $id = $db->loadResult();

        return $id;
    }//function

    /**
     * Discover all projects.
     *
     * @param string $scope The scope - admin or site.
     *
     * @return array
     */
    public function getAllProjects($scope)
    {
        $projects = array();

        switch($scope)
        {
            case 'admin':
                $projects = JFolder::folders(JPATH_ADMINISTRATOR.DS.'templates');
                break;

            case 'site':
                $projects = JFolder::folders(JPATH_SITE.DS.'templates');
                break;
        }//switch

        return $projects;
    }//function

    /**
     * Get a list of known core projects.
     *
     * @param string $scope The scope - admin or site.
     *
     * @return array
     */
    public function getCoreProjects($scope)
    {
        $projects = array();

        switch($scope)
        {
            case 'admin':
                switch(ECR_JVERSION)
                {
                    case '1.5':
                        $projects = array('khepri', 'system');
                        break;

                    case '1.6':
                    case '1.7':
                        $projects = array('bluestork', 'hathor', 'system');
                        break;
                    default:
                        JError::raiseWarning(0, __METHOD__.' - Unknown J! version');
                        break;
                }//switch
                break;

            case 'site':
                switch(ECR_JVERSION)
                {
                    case '1.5':
                        $projects = array('beez', 'ja_purity', 'rhuk_milkyway', 'system');
                        break;
                    case '1.6':
                        $projects = array('atomic', 'beez_20', 'beez5', 'rhuk_milkyway', 'system');
                        break;
                    case '1.7':
                        $projects = array('atomic', 'beez_20', 'beez5', 'system');
                        break;
                    default:
                        JError::raiseWarning(0, __METHOD__.' - Unknown J! version');
                        break;
                }//switch
                break;

            default:
                JError::raiseWarning(100, 'EasyProjectTemplate::getCoreProjects Unknown scope '.$scope);

                return array();
                break;
        }//switch

        return $projects;
    }//function
}//class
