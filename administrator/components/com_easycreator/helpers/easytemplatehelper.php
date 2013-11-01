<?php
/**
 * @version SVN: $Id: easytemplatehelper.php 418 2011-06-13 04:01:43Z elkuku $
 * @package    EasyCreator
 * @subpackage Helpers
 * @author     Nikolai Plath {@link http://www.nik-it.de}
 * @author     Created on 17-Oct-2009
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

/**
 * EasyCreator template helper.
 *
 * @package    EasyCreator
 */
class EasyTemplateHelper
{
    /**
     * Gets a list of installed templates.
     *
     * @return array Indexed array with template type as key ans folder name as value
     */
    public static function getTemplateList()
    {
        static $list = array();

        if(count($list))
        {
            return $list;
        }

        $types = JFolder::folders(ECRPATH_EXTENSIONTEMPLATES);

        foreach($types as $tplType)
        {
            if($tplType == 'parts'
            || $tplType == 'std'
            || $tplType == 'autocodes')
            {
                continue;
            }

            $templates = JFolder::folders(ECRPATH_EXTENSIONTEMPLATES.DS.$tplType);

            foreach($templates as $tplName)
            {
                if( ! $info = self::getTemplateInfo($tplType, $tplName))
                {
                    continue;
                }

                $list[$tplType][] = $info;
            }//foreach
        }//foreach

        return $list;
    }//function

    /**
     * Gets Information about a specific template.
     *
     * @param string $tplType Template type
     * @param string $tplName Template name
     *
     * @return object Template info.
     */
    public static function getTemplateInfo($tplType, $tplName)
    {
        if( ! JFile::exists(ECRPATH_EXTENSIONTEMPLATES.DS.$tplType.DS.$tplName.DS.'manifest.xml'))
        {
            return false;
        }

        $xml = EasyProjectHelper::getXML(ECRPATH_EXTENSIONTEMPLATES.DS.$tplType.DS.$tplName.DS.'manifest.xml');

        $info = new stdClass;
        $info->folder = $tplName;
        $info->name = (string)$xml->name;
        $info->description = jgettext((string)$xml->description);
        $info->version = (string)$xml->version;
        $info->jVersion = (string)$xml->jVersion;
        $info->phpVersion = (string)$xml->phpVersion;
        $info->dbTables = (string)$xml->dbTables;
        $info->author = (string)$xml->author;
        $info->authorUrl = (string)$xml->authorUrl;

        $info->info = '';
        $info->info .= jgettext(ucfirst($tplType)).' '.$info->name.' '.$info->version.'::'.$info->description;
        $info->info .=($info->author) ? '<br /><span style=\'color: blue;\'>Author:</span> '.$info->author : '';
        $info->info .= '<br /><strong>Joomla! version:</strong> '.$info->jVersion;
        $info->info .= '<br /><strong>PHP version:</strong> '.$info->phpVersion;
        $info->info .=($info->dbTables) ? '<br /><span style=\'color: orange;\'>dbTables:</span> '.$info->dbTables : '';
        $info->info .= '<br />ECR Folder: '.$info->folder;

        return $info;
    }//function

    /**
     * Export templates to a tar.gz package.
     *
     * @param array $exports Index array of templates to export
     *
     * @return boolean true on success
     */
    public static function exportTemplates($exports)
    {
        ecrLoadHelper('archive');

        $tempDir = JFactory::getConfig()->getValue('config.tmp_path').DS.uniqid('templateexport');

        $files = array();

        foreach($exports as $type => $folders)
        {
            foreach($folders as $folder)
            {
                $fileList = JFolder::files(ECRPATH_EXTENSIONTEMPLATES.DS.$type.DS.$folder, '.', true, true);

                foreach($fileList as $path)
                {
                    $path = str_replace(ECRPATH_EXTENSIONTEMPLATES.DS, '', $path);

                    if( ! JFolder::exists(dirname($tempDir.DS.$path)))
                    {
                        JFolder::create(dirname($tempDir.DS.$path));
                    }

                    if( ! JFile::copy(ECRPATH_EXTENSIONTEMPLATES.DS.$path, $tempDir.DS.$path))
                    throw new Exception(sprintf(jgettext('Unable to copy the file %s to %s')
                    , ECRPATH_EXTENSIONTEMPLATES.DS.$path, $tempDir.DS.$path));

                    $files[] = $tempDir.DS.$path;
                }//foreach
            }//foreach
        }//foreach

        $xml = new SimpleXMLElement('<install type="ecrextensiontemplate" version="'.ECR_VERSION.'"/>');

        $doc = new DOMDocument('1.0', 'utf-8');
        $doc->formatOutput = true;

        $domnode = dom_import_simplexml($xml);
        $domnode = $doc->importNode($domnode, true);
        $domnode = $doc->appendChild($domnode);

        if( ! JFile::write($tempDir.DS.'manifest.xml', $doc->saveXML()))
        throw new Exception(sprintf(jgettext('Unable to write file %s'), $tempDir.DS.$fileName));

        $files[] = $tempDir.DS.'manifest.xml';

        $fileName = 'ecr_extension_templates'.date('Ymd_His').'.tar.gz';

        $result = EasyArchive::createTgz(ECRPATH_EXPORTS.DS.'templates'.DS.$fileName, $files, 'gz', $tempDir);

        //--This means error
        if( ! $result->listContent())
        throw new Exception(jgettext('Error creating archive'));

        return true;
    }//function

    /**
     * Install templates.
     *
     * @return boolean true on success
     */
    public static function installTemplates()
    {
        jimport('joomla.installer.helper');

        if( ! $package = self::_getPackageFromUpload())
        throw new Exception(jgettext('Unable to find install package'));

        if($package['type'] != 'ecrextensiontemplate')
        throw new Exception(jgettext('This is not an EasyCreator Extension Template'));

        $types = (JFolder::folders($package['extractdir']));

        foreach($types as $type)
        {
            JFolder::create(ECRPATH_EXTENSIONTEMPLATES.DS.$type);
            $templates = JFolder::folders($package['extractdir'].DS.$type);

            foreach($templates as $template)
            {
                //-- Check for previous install - no upgrade yet..
                if(JFolder::exists(ECRPATH_EXTENSIONTEMPLATES.DS.$type.DS.$template))
                throw new Exception(sprintf(jgettext('The template %s is already installed'), $type.' - '.$template));

                //-- Create template dir
                JFolder::create(ECRPATH_EXTENSIONTEMPLATES.DS.$type.DS.$template);

                //-- Create the folders
                $folders = JFolder::folders($package['extractdir'].DS.$type.DS.$template, '.', true, true);

                foreach($folders as $folder)
                {
                    $s = str_replace($package['extractdir'].DS.$type.DS.$template.DS, '', $folder);

                    if( ! JFolder::create(ECRPATH_EXTENSIONTEMPLATES.DS.$type.DS.$template.DS.$s))
                    throw new Exception(sprintf(jgettext('Can not create folder %s'), $folder));
                }//foreach

                //-- Copy the files
                $files = JFolder::files($package['extractdir'].DS.$type.DS.$template, '.', true, true);

                foreach($files as $file)
                {
                    $s = str_replace($package['extractdir'].DS.$type.DS.$template.DS, '', $file);

                    if( ! JFile::copy($file, ECRPATH_EXTENSIONTEMPLATES.DS.$type.DS.$template.DS.$s))
                    throw new Exception(jgettext('Can not copy file %s', $s));
                }//foreach
            }//foreach
        }//foreach

        return true;
    }//function

    /**
     * Upload and unpack a package file.
     *
     * @return mixed array the package on success | boolean false on error
     */
    private function _getPackageFromUpload()
    {
        //-- Get the uploaded file information
        $userfile = JRequest::getVar('install_package', null, 'files', 'array');

        //-- If there is no uploaded file, we have a problem...
        if( ! is_array($userfile))
        throw new Exception(jgettext('No file selected'));

        //-- Check if there was a problem uploading the file.
        if($userfile['error'] || $userfile['size'] < 1)
        throw new Exception(jgettext('Invalid package'));

        //-- Build the appropriate paths
        $tmp_src = $userfile['tmp_name'];
        $tmp_dest = JFactory::getConfig()->getValue('config.tmp_path').DS.$userfile['name'];

        //-- Move uploaded file
        $uploaded = JFile::upload($tmp_src, $tmp_dest);

        //-- Unpack the downloaded package file
        $package = JInstallerHelper::unpack($tmp_dest);

        return $package;
    }//function
}//class
