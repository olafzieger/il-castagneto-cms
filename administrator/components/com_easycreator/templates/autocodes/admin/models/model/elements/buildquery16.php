<?php
/**
 * @version SVN: $Id: buildquery16.php 386 2011-05-03 08:06:41Z elkuku $
 * @package
 * @subpackage
 * @author		Nikolai Plath {@link http://www.nik-it.de}
 * @author		Created on 22.03.2010
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

/**
 * Enter description here ...@todo class doccomment.
 *
 */
class AutoCodeAdminModelsModelElementBuildquery16
{
    /**
     * Get the AutoCode to insert.
     *
     * @param EasyTable $table A EasyTable object
     * @param string $indent The string to indent with
     *
     * @return string
     */
    public function getCode(EasyTable $table, $indent = '')
    {
        $ret = '';
        $fields = array();
        $aliases = '';
        $relations = '';
        $fields = '';
        $charCode = 98;// b

        foreach($table->getFields() as $field)
        {
            if( ! $field->display)
            {
                continue;
            }

            $fields[] = 'a.'.$field->name;
        }//foreach

        $fields = implode(', ', $fields);

        if(count($table->getRelations()))
        {
            foreach($table->getRelations() as $relation)
            {
                $rType = str_replace(' ', '', strtolower($relation->type));

                $relations .= $indent.'$query->'.$rType.'(\' #__'.$relation->onTable
                .' AS '.chr($charCode).' ON '.chr($charCode).'.'.$relation->onField.' = a.'.$relation->field.'\');';

                if(count($relation->aliases))
                {
                    foreach($relation->aliases as $alias)
                    {
                        $aliases .= ', '.chr($charCode).'.'.$alias->aliasField.' AS '.$alias->alias;
                    }//foreach
                }

                $charCode ++;
            }//foreach

            $relations .= NL;
        }

        $ret .= $indent.'$query->from(\'#__'.$table->name.' AS a \');'.NL;

        $ret .= $relations;

        $ret .= $indent.'$query->select(\''.$fields.$aliases.'\');'.NL;

        return $ret;
    }//function
}//class
