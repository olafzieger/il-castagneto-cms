<?php
/**
 * @version SVN: $Id: part.php 458 2011-07-26 07:47:24Z elkuku $
 * @package    EasyCreator
 * @subpackage	Parts
 * @author hidabe -- pls add your credits =;)
 * @author		Nikolai Plath (elkuku) {@link http://www.nik-it.de NiK-IT.de}
 * @author		Created on 20-Apr-2009
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

/**
 * Enter description here ...@todo class doccomment.
 *
 */
class PartTablesAdmin_create
{
    public $group = 'tables';

    /**
     * Info about the thing.
     *
     * @return EasyTemplateInfo
     */
    public function info()
    {
        $info = new EasyTemplateInfo;

        $info->group = $this->group;
        $info->title = 'Create and Admin';
        $info->description = jgettext('This will create a new table with admin interface');

        return $info;
    }//function

    /**
     * Get insert options.
     *
     * @return void
     */
    public function getOptions()
    {
        ?>
<strong><?php echo jgettext('Include Fields');?></strong>
<table>
	<tr>
		<td><?php echo jgettext('Id');?></td>
		<td><?php echo JHTML::_('select.booleanlist', 'pack_mat_id', 'readonly; disabled=true', 1);?></td>
	</tr>
	<tr>
		<td><?php echo jgettext('Title');?></td>
		<td><?php echo JHTML::_('select.booleanlist', 'pack_mat_title', 'readonly; disabled=true', 1);?></td>
	</tr>
	<tr>
		<td><?php echo jgettext('Description');?></td>
		<td><?php echo JHTML::_('select.booleanlist', 'pack_mat_description', '', 1);?></td>
	</tr>
	<tr>
		<td><?php echo jgettext('Published');?></td>
		<td><?php echo JHTML::_('select.booleanlist', 'pack_mat_published', '', 1);?></td>
	</tr>
	<tr>
		<td><?php echo jgettext('Ordering');?></td>
		<td><?php echo JHTML::_('select.booleanlist', 'pack_mat_ordering', '', 1);?></td>
	</tr>
</table>
        <?php
        ecrHTML::drawSelectName();
        ecrHTML::drawLoggingOptions();

        $requireds = array('element_name');
        ecrHTML::drawSubmitParts($requireds);
    }//function

    /**
     * Inserts the part into the project.
     *
     * @param EasyProject $easyProject The project.
     * @param array $options Insert options.
     * @param EasyLogger $logger The EasyLogger.
     *
     * @return boolean
     */
    public function insert(EasyProject $easyProject, $options, EasyLogger $logger)
    {
        $element_name = JRequest::getCmd('element_name');

        $element_params = array();
        $element_params['description'] = JRequest::getVar('pack_mat_description', 0);
        $element_params['published'] = JRequest::getVar('pack_mat_published', 0);
        $element_params['ordering'] = JRequest::getVar('pack_mat_ordering', 0);

        /*
         * Add substitutes
         */
        $easyProject->addSubstitute('_ECR_ELEMENT_NAME_', $element_name);
        $easyProject->addSubstitute('_ECR_LOWER_ELEMENT_NAME_', strtolower($element_name));
        $easyProject->addSubstitute('_ECR_TABLE_NAME_'
        , strtolower($easyProject->getSubstitute('_ECR_COM_NAME_').'_'.$element_name));

        /*
         * Read part options files
         */
        $files = JFolder::files($options->pathSource.DS.'options', '.', true, true);

        foreach($files as $file)
        {
            $fileContents = JFile::read($file);

            if(strpos($fileContents, '<?php') === 0)
            {
                $fileContents = substr($fileContents, 6);
            }

            $easyProject->substitute($fileContents);
            $easyProject->addSubstitute('##'.strtoupper(JFile::stripExt(JFile::getName($file))).'##', $fileContents);
        }//foreach

        /*
         * Add/remove substitutes
         */
        $tableVars = '';
        $tableVars .= $this->formatTableVar('id', 'int', '0', array('Primary key'));
        $tableVars .= $this->formatTableVar('title', 'varchar');

        if($element_params['description'])
        {
            $tableVars .= $this->formatTableVar('description', 'text');
        }
        else
        {
            $easyProject->addSubstitute('##ECR_MAT_DESCRIPTION_VIEW1##', '');
            $easyProject->addSubstitute('##ECR_MAT_DESCRIPTION_VIEW2##', '');
            $easyProject->addSubstitute('##ECR_SMAT_DESCRIPTION_VIEW0##', '');
            $easyProject->addSubstitute('##ECR_SMAT_DESCRIPTION_VIEW1##', '');
            $easyProject->addSubstitute('##ECR_SMAT_DESCRIPTION_CONTROLLER1##', '');
        }

        if($element_params['ordering'])
        {
            $tableVars .= $this->formatTableVar('ordering', 'int', '0');
        }
        else
        {
            $easyProject->addSubstitute('##ECR_MAT_ORDERING_VIEW0##', '');
            $easyProject->addSubstitute('##ECR_MAT_ORDERING_VIEW1##', '');
            $easyProject->addSubstitute('##ECR_MAT_ORDERING_VIEW2##', '');
            $easyProject->addSubstitute('##ECR_MAT_ORDERING_MODAL1##', "");
            $easyProject->addSubstitute('##ECR_MAT_ORDERING_CONTROLLER1##', '');
        }

        if($element_params['published'])
        {
            $tableVars .= $this->formatTableVar('published', 'int', '0');
        }
        else
        {
            $easyProject->addSubstitute('##ECR_MAT_PUBLISHED_VIEW1##', '');
            $easyProject->addSubstitute('##ECR_MAT_PUBLISHED_VIEW11##', '');
            $easyProject->addSubstitute('##ECR_MAT_PUBLISHED_VIEW2##', '');
            $easyProject->addSubstitute('##ECR_SMAT_PUBLISHED_VIEW0##', '');
            $easyProject->addSubstitute('##ECR_SMAT_PUBLISHED_VIEW1##', '');
        }

        $easyProject->addSubstitute('##ECR_TABLE_VARS##', $tableVars);

        /*
         * Process files
         */
        JRequest::setVar('element_scope', 'admin');

        if( ! $easyProject->insertPart($options, $logger))
        {
            return false;
        }

        /*
         * Create the table
         */
        $db =& JFactory::getDBO();
        $query = 'CREATE TABLE #__'.$easyProject->getSubstitute('_ECR_TABLE_NAME_').' ('
        . '`id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY'
        . ', `title` VARCHAR( 255 ) NOT NULL';

        if($element_params['description'])
        $query .= ', `description` TEXT NOT NULL';

        if($element_params['published'])
        $query .= ', `published` TINYINT( 1 ) NOT NULL';

        if($element_params['ordering'])
        $query .= ', `ordering` INT( 11 ) NOT NULL';

        $query .= ') ENGINE = MYISAM ';
        $db->setQuery($query);

        if( ! $db->query())
        {
            JError::raiseWarning(100, $db->getError());
            $logger->logQuery($query, $db->getError());

            return false;
        }

        $logger->logQuery($query);

        return true;
    }//function

    /**
     * Format variables to be displayed as docComment in class header.
     *
     * @param string $var Variable name
     * @param string $type Data type
     * @param string $def Default value
     * @param string $adds Additional comments
     *
     * @return string
     */
    private function formatTableVar($var, $type, $def = 'NULL', $adds = array())
    {
        $string = '';
        $string .= '	/**'.NL;
        $string .= '	 * @var '.$type.NL;

        foreach($adds as $add)
        {
            $string .= '	 * '.$add.NL;
        }//foreach

        $string .= '	 */'.NL;
        $string .= '	var $'.$var.' = '.$def.';'.NL.NL;

        return $string;
    }//function
}//class
