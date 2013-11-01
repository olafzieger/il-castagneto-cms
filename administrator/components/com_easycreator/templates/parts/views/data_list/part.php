<?php
/**
 * @version SVN: $Id: part.php 458 2011-07-26 07:47:24Z elkuku $
 * @package    EasyCreator
 * @subpackage  Parts
 * @author      Nikolai Plath (elkuku) {@link http://www.nik-it.de NiK-IT.de}
 * @author      Created on 01-Sep-2009
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

/**
 * Enter description here ...@todo class doccomment.
 *
 */
class PartViewsData_list extends EasyPart
{
    public $group = 'views';

    public $name = 'data_list';

    public $patterns = array();

    public $keys = array();

    /**
     * Constructor.
     *
     * @param string $element Element name
     * @param string $scope Scope name
     */
    public function __construct($element, $scope)
    {
        $this->keys['##ECR_VIEW1_TMPL1_THS##'] = '##ECR_KEY##';
        $this->keys['##ECR_VIEW1_TMPL1_TDS##'] = '##ECR_KEY##';
        $this->patterns['##ECR_VIEW1_TMPL1_THS##'] = '    <th>'.NL
           ."        <?php echo JHTML::_('grid.sort', '##ECR_KEY##', '##ECR_KEY##', \$this->lists['order_Dir']"
           .", \$this->lists['order']);?>".NL
           .'    </th>'.NL;
        $this->patterns['##ECR_VIEW1_TMPL1_TDS##'] = '    <td>'.NL
        .'        <?php echo $row->##ECR_KEY##; ?>'.NL
        .'    </td>'.NL;
        $this->_element = $element;
        $this->_scope = $scope;

        parent::__construct($this->group, $this->name, $element, $scope);
    }//function
    /**
    * Info about the thing.
    *
    * @return EasyTemplateInfo
    */
    public function info()
    {
        $info = new EasyTemplateInfo;

        $info->group = $this->group;
        $info->title = 'Data List';
        $info->description = jgettext('Lists Data from a specific table.');

        return $info;
    }//function

    /**
     * Get insert options.
     *
     * @return void
     */
    public function getOptions()
    {
        /* Array with required fields */
        $requireds = array();

        $tableName = JRequest::getCmd('element');

        $db = JFactory::getDBO();
        $tables = $db->getTableList();

        $requireds[] = ecrHTML::drawSelectName($tableName);
        $requireds[] = ecrHTML::drawSelectScope($this->_scope);

        if( ! $tableName)
        {
            $ajaxLink = 'index.php?option=com_easycreator&tmpl=component&controller=ajax&task=part_task';
            $ajaxLink .= '&group=views&part=data_list';
            $ajaxLink .= '&part_task=show_tablefields';

            $ajax = "new Request({ url: '".$ajaxLink."'+'&element='+this.value,"
            ." update:'addPartTableFields', onComplete: function() { div_new_element.show(); } }).send();";

            echo '<strong id="table_name_label">'.jgettext('Table').' :</strong>';
            echo '<select name="table_name" id="table_name" onchange="'.$ajax.'">';
            echo '<option value="">'.jgettext('Choose...').'</option>';

            $prefix = $db->getPrefix();

            foreach($tables as $table)
            {
                $v = str_replace($prefix, '', $table);
                echo '<option value="'.$v.'">'.$v.'</option>';
            }//foreach
            echo '</select>';

            echo '<div id="addPartTableFields"></div>';
        }
        else
        {
            $selecteds = array();//todo
            $this->show_tablefields($tableName, $selecteds);
            echo '<input type="hidden" name="table_name" value="'.$tableName.'" />';
        }

        echo '<strong>'.jgettext('Menu link:').'</strong><br />';
        echo '<input type="checkbox" name="create_menu_link" checked="checked" '
        .'id="create_menu_link" value="create_menu_link">';

        echo '<label for="create_menu_link">'.jgettext('Create a menu link').'</label><br />';
        echo '<hr />';

        ecrHTML::drawLoggingOptions();

        ecrHTML::drawSubmitParts($requireds);
    }//function

    /**
     * Edit the AutoCode.
     *
     * @param object $AutoCode The AutoCode
     *
     * @return boolean
     */
    public function edit($AutoCode)
    {
        /* Array with required fields */
        $requireds = array();

        $requireds[] = ecrHTML::drawSelectScope($this->_scope);
        echo '<input type="hidden" name="element" value="'.$this->_element.'" />';

        /* Draws an input box for a name field */
        $requireds[] = ecrHTML::drawSelectName($this->_element, jgettext('Table'));
//        JLoader::import('helpers.autocode', JPATH_COMPONENT);
//        $AutoCode = new EasyAutoCode;

        $ecr_project = JRequest::getCmd('ecr_project');
        $element_name = JRequest::getCmd('element');
        $table_name = JRequest::getCmd('table_name');

        if( ! $table_name)
        {
            $table_name = $element_name;
        }

        $req_table_fields = JRequest::getVar('table_fields', array());
        $req_table_fields_edits = JRequest::getVar('table_fields_edits', array());
        $req_table_fields_types = JRequest::getVar('table_fields_types', array());

        if( ! $table_name)
        {
            JError::raiseWarning(100, jgettext('No table given'));

            return false;
        }

        if( ! $scope = JRequest::getCmd('scope'))
        {
            JError::raiseWarning(100, jgettext('No scope given'));

            return false;
        }

        $basePath =($scope == 'admin') ? JPATH_ADMINISTRATOR : JPATH_SITE;
        $basePath .= DS.'components'.DS.$ecr_project.DS.'views'.DS.$table_name.'s';

        if( ! JFolder::exists($basePath))
        {
            JError::raiseWarning(100, jgettext('View not found'));

            return false;
        }

        $fileList = JFolder::files($basePath, '.', true, true);

        $fields = array();

        if(count($fileList))
        {
            foreach($fileList as $fileName)
            {
                $file = new stdClass;
                $file->path = str_replace(JPATH_ROOT.DS, '', $fileName);
                $file->autoCodes = $AutoCode->getContents(JFile::read($fileName));

                foreach($file->autoCodes as $key => $content)
                {
                    $fields[$key] = $AutoCode->getFields($this->patterns[$key], $content, array($this->keys[$key]));
                }//foreach
            }//foreach
        }

        $selecteds =(isset($fields['##ECR_VIEW1_TMPL1_THS##'])) ? $fields['##ECR_VIEW1_TMPL1_THS##'] : array(array());

        $key = key($this->keys);

        if(isset($AutoCode->fields[$key])
            && count($AutoCode->fields[$key]))
        {
            $selecteds = $AutoCode->fields[$key];
        }

        $this->show_tablefields($table_name, $selecteds);

        ecrHTML::drawSubmitParts();
    }//function

    /**
     * Inserts the part into the project.
     *
     * @param EasyProject $EasyProject The project.
     * @param array $options Insert options.
     * @param EasyLogger $logger The EasyLogger.
     *
     * @return boolean
     */
    public function insert(EasyProject $EasyProject, $options, EasyLogger $logger)
    {
        ecrLoadHelper('autocode');

        $EasyProject->addSubstitute('_ECR_SUBPACKAGE_', 'Models');

        $element_name = JRequest::getCmd('element');
        $element_scope = JRequest::getCmd('element_scope');

        $table_name = JRequest::getCmd('table_name');

        if( ! $table_name)
        {
            $table_name = $element_name;
        }

        $req_table_fields = JRequest::getVar('table_fields', array());

        if( ! $table_name)
        {
            JError::raiseWarning(100, jgettext('No table given'));

            return false;
        }

        $db = JFactory::getDBO();
        $prefix = $db->getPrefix();
        $fields = $db->getTableFields($prefix.$table_name);

        $tableFields = array();
        $table_vars = '';

        if(count($fields))
        {
            $tableFields = $fields[$prefix.$table_name];
        }

        /*
         * Add substitutes
         */
        $EasyProject->addSubstitute('_ECR_ELEMENT_NAME_', $element_name);
        $EasyProject->addSubstitute('_ECR_LOWER_ELEMENT_NAME_', strtolower($element_name));
        $EasyProject->addSubstitute('_ECR_TABLE_NAME_', $table_name);

        /*
         * Add manual substitutes
         */
        $substitutes = array();

        foreach($this->patterns as $pKey => $pReplacement)
        {
            $substitutes[$pKey] = '';
        }//foreach

        $i = 0;

        foreach($tableFields as $key => $value)
        {
            if( ! in_array($key, $req_table_fields))
            {
                continue;
            }

            foreach($this->patterns as $pKey => $pReplacement)
            {
                $substitutes[$pKey] .= str_replace('##ECR_KEY##', $key, $pReplacement);
            }//foreach

            $i ++;
        }//foreach

        $substitutes['##ECR_VIEW1_TMPL1_THS##'] .= '    <?php $coloumnCount += '.$i.'; ?>'.NL;

        $AutoCode = new EasyAutoCode($this->group, $this->name, $table_name, $element_scope);

        $AutoCode->options = array();

        foreach($substitutes as $key => $value)
        {
            $AutoCode->fields[$key] = $req_table_fields;//array();//$autoCodeFields;
            $AutoCode->codes[$key] = $AutoCode->enclose($value, $key, true);
            $EasyProject->addSubstitute($key, $AutoCode->enclose($value, $key, true));
        }//foreach

        $EasyProject->addAutoCode($AutoCode, $this->key);

        return $EasyProject->insertPart($options, $logger);
    }//function

    /**
     * Shows the fields of a given table.
     *
     * @param string $table_name Name of the table
     * @param array $selecteds Array of selected table fields
     *
     * AJAX called
     *
     * @return void
     */
    public function show_tablefields($table_name, $selecteds = array())
    {
        if( ! $table_name)
        {
            return;
        }

        $db = JFactory::getDBO();

        $table_name = $db->getPrefix().$table_name;
        $fields = $db->getTableFields($table_name);

        if( ! count($fields) || ! count($fields[$table_name]))
        {
            JError::raiseWarning(100, jgettext('No table fields found'));
        }

        ?>
<h4><?php echo jgettext('Include')?></h4>
        <?php
        foreach($fields[$table_name] as $key => $value)
        {
            //@todo db type fields
            switch($value)
            {
                //@todo
                case 'int':
                case 'tinyint':
                    $def = '0';
                    break;

                default:
                    $def = 'NULL';
                    break;
            }//switch
            $checked =(in_array($key, $selecteds)) ? ' checked="checked"' : '';
            ?>
<input type="checkbox" name="table_fields[]"
	id="tblfield_<?php echo $key; ?>"
	value="<?php echo $key; ?>"
	<?php echo $checked; ?>>
<label for="tblfield_<?php echo $key; ?>">
<?php echo $key.' ('.$value.')'; ?>
</label>
<br />
	<?php
        }//foreach
    }//function
}//class
