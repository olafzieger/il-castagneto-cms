<?php
/**
 * @version SVN: $Id: part.php 458 2011-07-26 07:47:24Z elkuku $
 * @package    EasyCreator
 * @subpackage	Parts
 * @author		Nikolai Plath (elkuku) {@link http://www.nik-it.de NiK-IT.de}
 * @author		Created on 20.04.2009
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

/**
 * Enter description here ...@todo class doc comment.
 *
 */
class PartTablesSimple extends EasyPart
{
    public $group = 'tables';

    public $name = 'simple';

    private $_varScopes = array(
    'var'
    , 'private'
    , 'protected'
    , 'public'
    );

    /**
     * Constructor.
     *
     * @param string $element The element name
     * @param string $scope Scope name
     */
    public function __construct($element, $scope)
    {
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
        $info->title = 'Data';
        $info->description = jgettext('This will map an existing table to a table class');

        return $info;
    }//function

    /**
     * Here you define custom options that will be displayed along with the input form.
     *
     * @return void
     */
    public function getOptions()
    {
        /* Array with required fields */
        $requireds = array();

        $requireds[] = ecrHTML::drawSelectScope($this->_scope);

        if( ! $this->_element)
        {
            $db = JFactory::getDBO();
            $tables = $db->getTableList();

            echo '<strong id="element_label">'.jgettext('Table').'</strong> : ';
            echo '<select name="element" id="table_name" onchange="$(\'element_name\').value=$(\'element\').value;">';
            echo '<option value="">'.jgettext('Choose...').'</option>';

            $prefix = $db->getPrefix();

            foreach($tables as $table)
            {
                $v = str_replace($prefix, '', $table);
                echo '<option value="'.$v.'">'.$v.'</option>';
            }//foreach

            echo '</select>';
            echo '<br />';
        }
        else
        {
            echo '<input type="hidden" name="element" value="'.$this->_element.'" />';
        }

        /* Draws an input box for a name field */
        $requireds[] = ecrHTML::drawSelectName($this->_element, jgettext('Table'));

        echo '<strong>Var Scope:</strong><br />';

        foreach($this->_varScopes as $vScope)
        {
            $checked =($vScope == 'var') ? ' checked="checked"' : '';
            echo '<input type="radio" name="var_scope" value="'.$vScope.'" id="vscope-'.$vScope.'"'
            .$checked.'> <label for="vscope-'.$vScope.'">'.$vScope.'</label><br />';
        }//foreach

        /*
         * Add your custom options
         * ...
         */

        /* Displays options for logging */
        ecrHTML::drawLoggingOptions();

        /* Draws the submit button */
        ecrHTML::drawSubmitParts($requireds);
    }//function

    /**
     * Open the part for edit.
     *
     * @param object $AutoCode The AutoCode
     *
     * @return void
     */
    public function edit($AutoCode)
    {
        $var_scope = $AutoCode->options['varScope'];

        /* Array with required fields */
        $requireds = array();

        $requireds[] = ecrHTML::drawSelectScope($this->_scope);
        echo '<input type="hidden" name="element" value="'.$this->_element.'" />';

        /* Draws an input box for a name field */
        $requireds[] = ecrHTML::drawSelectName($this->_element, jgettext('Table'));

        echo '<strong>Var Scope:</strong><br />';

        foreach($this->_varScopes as $vScope)
        {
            $checked =($vScope == $var_scope) ? ' checked="checked"' : '';
            echo '<input type="radio" name="var_scope" value="'.$vScope.'" id="vscope-'.$vScope.'"'
            .$checked.'> <label for="vscope-'.$vScope.'">'.$vScope.'</label><br />';
        }//foreach

        /* Draws the submit button */
        ecrHTML::drawSubmitParts($requireds);
    }//function

    /**
     * Inserts the part into the project.
     *
     * @param EasyProject $project The project.
     * @param array $options Insert options.
     * @param EasyLogger $logger The EasyLogger.
     *
     * @return boolean
     */
    public function insert(EasyProject $project, $options, EasyLogger $logger)
    {
        ecrLoadHelper('autocode');

        $table_name = JRequest::getCmd('element');
        $var_scope = JRequest::getCmd('var_scope');
        $element_scope = JRequest::getCmd('element_scope');

        if( ! $table_name)
        {
            JError::raiseWarning(100, jgettext('No table given'));

            return false;
        }

        $db = JFactory::getDBO();
        $prefix = $db->getPrefix();
        $fields = $db->getTableFields($prefix.$table_name);

        $table_vars = '';
        $autoCodeFields = array();

        if(count($fields))
        {
            foreach($fields[$prefix.$table_name] as $key => $value)
            {
                $table_vars .= $this->formatTableVar($key, $value, array(), $var_scope);
                $autoCodeFields[] = $key;
            }//foreach
        }

        $AutoCode = new EasyAutoCode($this->group, $this->name, $table_name, $element_scope);

        $AutoCode->options = array();
        $AutoCode->options['varScope'] = $var_scope;

        $AutoCode->fields['##ECR_TABLE_VARS##'] = array();//$autoCodeFields;
        $AutoCode->codes['##ECR_TABLE_VARS##'] = $AutoCode->enclose($table_vars, '##ECR_TABLE_VARS##');

        $AutoCode->fields['##ECR_FIELD_1##'] = array();
        $AutoCode->codes['##ECR_FIELD_1##'] = $AutoCode->enclose($this->formatTableVar('db'
        , 'Database object', array(), $var_scope), '##ECR_FIELD_1##');

        $project->addAutoCode($AutoCode, $this->key);

        /*
         * Add substitutes
         *
         * Define keys that will be substitutes in the code
         */
        $project->addSubstitute('_ECR_SUBPACKAGE_', 'Tables');
        $project->addSubstitute('_ECR_TABLE_NAME_', $table_name);

        foreach($AutoCode->codes as $key => $code)
        {
            $project->addSubstitute($key, $code);
        }//foreach

        /* Insert the part to your project and return the results */
        return $project->insertPart($options, $logger);
    }//function

    /**
     * Format variables to be displayed as docComment in class header.
     *
     * @param string $var Variable name
     * @param string $type Data type
     * @param string $adds Additional comments
     * @param string $varScope Variable scope
     *
     * @return string
     */
    private function formatTableVar($var, $type = '', $adds = array(), $varScope = 'var')
    {
        switch($type)
        {
            case 'int':
            case 'tinyint':
                $def = '0';
                break;

            default:
                $def = 'null';
                break;
        }//switch

        $s = '';
        $s .= '	/**'.NL;
        $s .= '	 * @var '.$type.NL;

        foreach($adds as $a)
        {
            $s .= '	 * '.$a.NL;
        }//foreach

        $s .= '	 */'.NL;
        $s .= '	'.$varScope.' $'.$var.' = '.$def.';'.NL.NL;

        return $s;
    }//function
}//class
