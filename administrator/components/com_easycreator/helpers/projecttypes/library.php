<?php
/**
 * @version SVN: $Id: library.php 432 2011-06-23 19:19:52Z elkuku $
 * @package    EasyCreator
 * @subpackage ProjectTypes
 * @author     Nikolai Plath {@link http://www.nik-it.de}
 * @author     Created on 16-May-2010
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

/**
 * EasyCreator project type library.
 */
class EasyProjectLibrary extends EasyProject
{
    /**
     * Project type.
     *
     * @var string
     */
    public $type = 'library';

    /**
     * Project prefix.
     *
     * @var string
     */
    public $prefix = 'lib_';

    public $JCompat = '1.6';

    /**
     * Find all files and folders belonging to the project.
     *
     * @return array
     */
    public function findCopies()
    {
        if($this->copies)
        return $this->copies;

        if(JFolder::exists(JPATH_LIBRARIES.DS.$this->comName))
        {
            $this->copies[] = JPATH_LIBRARIES.DS.$this->comName;
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
        JError::raiseWarning(0, __METHOD__.' unfinished');
        $scopes = array();
        $scopes[] =($this->scope) == 'admin' ? 'admin' : 'site';

        return $scopes;
    }//function

    /**
     * Gets the paths to language files.
     *
     * @return array
     */
    public function getLanguagePaths()
    {
        return array('site' => JPATH_SITE);
    }//function

    /**
     * Get the name for language files.
     *
     * @return string
     */
    public function getLanguageFileName()
    {
        return $this->prefix.$this->comName.'.ini';
    }//function

    /**
     * Gets the DTD for the extension type.
     *
     * @param string $jVersion Joomla! version
     *
     * @return mixed [array index array on success | false if not found]
     */
    public function getDTD($jVersion)
    {
        $dtd = false;

        switch(ECR_JVERSION)
        {
            case '1.6':
            case '1.7':
                break;

            default:
                ecrHTML::displayMessage(__METHOD__.' - Unknown J! version');
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
        return $this->prefix.$this->comName;
    }//function

    /**
     * Get the path for the Joomla! XML manifest file.
     *
     * @return string
     */
    public function getJoomlaManifestPath()
    {
        return JPATH_MANIFESTS.DS.'libraries';
    }//function

    /**
     * Get a Joomla! manifest XML file name.
     *
     * @return mixed [string file name | boolean false on error]
     */
    public function getJoomlaManifestName()
    {
        return $this->prefix.$this->comName.'.xml';
        //return $this->comName.'.xml';
    }//function

    /**
     * Get the project Id.
     *
     * @return integer Id
     */
    public function getId()
    {
        $db = JFactory::getDBO();

        $query = $db->getQuery(true);

        $query->from('#__extensions AS e');
        $query->select('e.extension_id');
        $query->where('e.type = '.$db->quote($this->type));
        $query->where('e.element = '.$db->quote($this->prefix.$this->comName));

        $db->setQuery($query);

        return $db->loadResult();
    }//function

    /**
     * Discover all projects.
     *
     * @param string $scope The scope - admin or site.
     *
     * @return array
     */
    public function getAllProjects()
    {
        return JFolder::folders(JPATH_LIBRARIES);
    }//function

    /**
     * Get a list of known core projects.
     *
     * @param string $scope The scope - admin or site.
     *
     * @return array
     */
    public function getCoreProjects()
    {
        switch(ECR_JVERSION)
        {
            case '1.6':
            case '1.7':
                return array('joomla', 'phpmailer', 'phputf8', 'simplepie');
                break;

            default:
                ecrHTML::displayMessage(__METHOD__.' - Unknown J! version');
                break;
        }//switch

        return array();
    }//function
}//class
