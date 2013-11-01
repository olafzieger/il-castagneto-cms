<?php
/**
 * @version SVN: $Id: header.php 18 2010-11-08 01:10:19Z elkuku $
 * @package    il-castagneto
 * @subpackage Html
 * @author     K.-Michael Siebenlist {@link http://www.kms-net.de}
 * @author     Created on 27-May-2011
 * @license    GNU/GPL
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

/**
 *
 * @param $module
 * @param $params
 * @param $attribs
 */
function modChrome_custom($module, $params, $attribs)
{
    // Überprüfen, ob "headerLevel" gesetzt ist
    if(isset($attribs['headerLevel']))
    {
        $headerLevel = $attribs['headerLevel'];
    }
    else
    {
        // default "3"
        $headerLevel = 3;
    }

    // Überprüfen, ob "class" gesetzt ist
    if(isset($attribs['class']))
    {
        $class = $attribs['class'];
    }
    else
    {
        // default "blue"
        $class = 'blue';
    }

    // umschließendes div mit Modul Klassen Suffix
    echo '<div class="'.$params->get('moduleclass_sfx').'" >';

    // Überprüfen, ob der Titel angezeigt wird
    if($module->showtitle)
    {
        // Titel ausgeben
        echo '<h'.$headerLevel.'>'.$module->title.'</h'.$headerLevel.'>';
    }

    // Content des Moduls ausgeben
    echo '<div class="'.$class.'">';
    echo $module->content;
    echo '</div>';

    // Ende umschließendes div
    echo '</div>';
}//function
