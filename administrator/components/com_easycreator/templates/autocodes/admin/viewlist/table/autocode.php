<?php
/**
 * @version $Id: autocode.php 418 2011-06-13 04:01:43Z elkuku $
 * @package		EasyCreator
 * @subpackage	AutoCodes
 * @author		Nikolai Plath (elkuku) {@link http://www.nik-it.de NiK-IT.de}
 * @author		Created on 07-Mar-2010
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

/**
 * Enter description here ...@todo class description.
 *
 */
class AutoCodeAdminViewlistTable extends EasyAutoCode
{
    protected $tags = array('start' => '<!--', 'end' => '-->');

    protected $enclose = 'php';

    private $indent = '            ';

    /**
     * Get the AutoCode to insert.
     *
     * @param string $type AutoCode type
     * @param EasyTable $table A EasyTable object
     *
     * @return string
     */
    public function getCode($type, EasyTable $table)
    {
        $element = $this->getElement($type, dirname(__FILE__));

        if( ! $element)
        {
            return false;
        }

        return $element->getCode($table, $this->indent);
    }//function

    /**
     * Open the AutoCode for edit.
     *
     * @param EasyAutoCode $AutoCode The AutoCode
     *
     * @return void
     */
    public function edit(EasyAutoCode $AutoCode)
    {
        /* Array with required fields */
        $requireds = array();

        $requireds[] = ecrHTML::drawSelectScope($this->scope);

        echo '<input type="hidden" name="element" value="'.$this->element.'" />';

        /* Draws an input box for a name field */
        $requireds[] = ecrHTML::drawSelectName($this->element, jgettext('Table'));

        $tableFields = EasyTableHelper::getTableFields($this->element);

        $elements = array('header', 'cell');

        echo '<br />';

        /*
         * Header
         */

        $key = $AutoCode->getKey().'.header';

        if(array_key_exists($key, $AutoCode->fields))
        {
            $acFields = $AutoCode->fields[$key];
        }
        else
        {
            $acFields = array();
        }

        echo '<div style="background-color: #f3fbe6;">Header</div>';
        echo '<table>';
        echo '<tr>';
        echo '<th>'.jgettext('Field').'</th>';
        echo '<th>'.jgettext('Label').'</th>';
        echo '<th>'.jgettext('Display').'</th>';
        echo '<th>'.jgettext('Width').'</th>';
        echo '</tr>';

        foreach($tableFields as $name => $tableField)
        {
            if(array_key_exists($name, $acFields))
            {
                //-- Autocode present
                $label = $acFields[$name]->label;
                $display = $acFields[$name]->display;
                $width = $acFields[$name]->width;
            }
            else
            {
                //-- New field
                $label = $tableField->Field;
                $display = 'on';
                $width = 0;
            }

            echo '<tr>';
            echo '<th>';
            echo $name;
            echo '</th>';

            echo '<td>';
            echo '<input type="text" name="field[header]['.$name.'][label]" value="'.$label.'" />';
            echo '</td>';
            echo '<td>';
            echo '<select name="field[header]['.$name.'][display]">';
            $selected =($display == 'on') ? ' selected="selected"' : '';
            echo '<option value="on"'.$selected.'>'.jgettext('On').'</option>';
            $selected =($display == 'off') ? ' selected="selected"' : '';
            echo '<option value="off"'.$selected.'>'.jgettext('Off').'</option>';
            echo '</select>';
            echo '</td>';
            echo '<td>';
            echo '<input type="text" name="field[header]['.$name.'][width]" value="'.$width.'" />';
            echo '</td>';
            echo '</tr>';
        }//foreach
        echo '</table>';

        /*
         * Cell
         */
        $key = $AutoCode->getKey().'.cell';

        if(array_key_exists($key, $AutoCode->fields))
        {
            $acFields = $AutoCode->fields[$key];
        }
        else
        {
            $acFields = array();
        }

        echo '<div style="background-color: #f3fbe6;">Cell</div>';
        echo '<table>';
        echo '<tr>';
        echo '<th>'.jgettext('Field').'</th>';
        //            echo '<th>'.jgettext('Label').'</th>';
        echo '</tr>';

        foreach($tableFields as $name => $tableField)
        {
            if(array_key_exists($name, $acFields))
            {
                //-- Autocode present
                $value = $acFields[$name]->label;
            }
            else
            {
                //-- New field
                $value = $tableField->Field;
            }

            echo '<tr>';
            echo '<th>';
            echo $name;
            echo '<input type="hidden" name="field[cell]['.$name.'][name]" value="'.$name.'" />';
            echo '</th>';

            echo '</tr>';
        }//foreach
        echo '</table>';

        /* Draws the submit button */
        ecrHTML::drawSubmitAutoCode($requireds);
    }//function

    /**
     * Inserts the AutoCode into the project.
     *
     * @param EasyProject $project The project.
     * @param array $options Insert options.
     * @param EasyLogger $logger The EasyLogger.
     *
     * @return boolean
     */
    public function insert(EasyProject $project, $options, EasyLogger $logger)
    {
        $table_name = JRequest::getCmd('element');
        $element_scope = JRequest::getCmd('element_scope');

        if( ! $table_name)
        {
            JError::raiseWarning(100, jgettext('No table given'));

            return false;
        }

        $table = new EasyTable($table_name);

        $fields = EasyTableHelper::getTableFields($table_name);

        $reqFields = JRequest::getVar('field');

        $headers = '';
        $cells = '';

        $tags = array('<!--', '-->');
        $indent = '            ';

        if( ! count($fields))
        {
            JError::raiseWarning(100, 'No table fields found');

            return false;
        }

        foreach($fields as $name => $field)
        {
            $reqFieldHeader = $reqFields['header'][$name];
            $reqFieldCell = $reqFields['cell'][$name];

            if(isset($project->autoCodes[$this->key]->fields[$this->key.'.header'][$name]))
            {
                $fieldHeader = $project->autoCodes[$this->key]->fields[$this->key.'.header'][$name];
                $fieldHeader->label = $reqFieldHeader['label'];
            }
            else
            {
                $fieldHeader = new EasyTableField;
                $fieldHeader->name = $name;
                $fieldHeader->label = $name;
            }

            if(isset($project->autoCodes[$this->key]->fields[$this->key.'.cell'][$name]))
            {
                $fieldCell = $project->autoCodes[$this->key]->fields[$this->key.'.cell'][$name];
            }
            else
            {
                $fieldCell = new EasyTableField;
                $fieldCell->name = $name;
            }

            $fieldHeader->display = $reqFieldHeader['display'];
            $fieldHeader->width = $reqFieldHeader['width'];

            //-- Display value for cells is the same as for headers
            $fieldCell->display = $reqFieldHeader['display'];

            $autoCodeFieldsHeader[] = $fieldHeader;
            $autoCodeFieldsCell[] = $fieldCell;

            $table->addField($fieldHeader);
        }//foreach
            $headers .= $this->getCode('header', $table, $indent);

            $cells .= $this->getCode('cell', $table, $indent);

        $this->fields[$this->key.'.header'] = $autoCodeFieldsHeader;
        $this->codes[$this->key.'.header'] = $this->enclose($headers, $this->key.'.header');

        $this->fields[$this->key.'.cell'] = $autoCodeFieldsCell;
        $this->codes[$this->key.'.cell'] = $this->enclose($cells, $this->key.'.cell');

        $project->addAutoCode($this);

        /*
         * Add substitutes
         *
         * Define keys that will be substitutes in the code
         */
        $project->addSubstitute('_ECR_SUBPACKAGE_', 'Views');
        $project->addSubstitute('_ECR_TABLE_NAME_', $table_name);

        foreach($this->codes as $key => $code)
        {
            $project->addSubstitute($tags[0].$key.$tags[1], $code);
        }//foreach

        /* Insert the part to your project and return the results */
        return $project->insertPart($options, $logger);
    }//function
}//class
