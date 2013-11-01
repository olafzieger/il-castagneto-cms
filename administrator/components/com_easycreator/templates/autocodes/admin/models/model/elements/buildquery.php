<?php
/**
 * @version SVN: $Id: buildquery.php 18 2010-11-08 01:10:19Z elkuku $
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
class AutoCodeAdminModelsModelElementBuildquery
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
                $relations .= $indent.".' ".$relation->type.' #__'.$relation->onTable
                .' AS '.chr($charCode).' ON '.chr($charCode).'.'.$relation->onField.' = a.'.$relation->field."'";

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

        $ret .= $indent.'$query = \' SELECT '.$fields.$aliases."'".NL
        .$indent.'.\' FROM #__'.$table->name.' AS a \''.NL
        .$relations.';';

        $ret .= NL;

        return $ret;
    }//function
}//class
