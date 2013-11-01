<?php
/**
 * @version SVN: $Id: phpcpd.php 434 2011-07-02 20:27:06Z elkuku $
 * @package    EasyCreator
 * @subpackage Views
 * @author		Nikolai Plath {@link http://www.nik-it.de}
 * @author		Created on 28-Sep-2009
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

ecrLoadHelper('pearhelpers.phpcpd');

$phpcpd = new EasyPHPCPD;

if( ! $phpcpd->checkEnv()):
    echo 'Env check failed.. cannot continue :(';

    return;
endif;
?>

<div class="ecr_floatbox">
    Min-Lines:<br />
    <input type="text" id="phpcpd_min_lines" size="5" value="5" />
    <br />
    Min-Tokens:<br />
    <input type="text" id="phpcpd_min_tokens" size="5" value="70" />
    <br /><br />
    <!--
    @todo activate when phpcpd supports multiple dirs
    <div class="ecr_button" onclick="doPHPCPD('<?php echo $this->ecr_project; ?>');">Find duplicated Code</div>
     -->
</div>
<div class="ecr_floatbox">
    <?php
    foreach($this->project->copies as $dir):
        if(is_dir($dir)):
            $d = str_replace(JPATH_ROOT.DS, '', $dir);
            echo '<div onclick="setPath(\''.$d.'\'); doPHPCPD();" class="ecr_button">'.$d.'</div>';
        endif;
    endforeach;
    ?>
</div>

<div style="clear: both;"></div>

<span id="dspl_sniff_folder"></span>
<br />
<div id="ecr_title_file"></div>
<div id="ecr_codeeye_output" style="padding-top: 0.2em;"><h2><?php echo jgettext('Output')?></h2></div>
<pre id="ecr_codeeye_console"><?php echo jgettext('Console'); ?></pre>
