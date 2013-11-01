<?php
/**
 * @version SVN: $Id: selenium.php 434 2011-07-02 20:27:06Z elkuku $
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath {@link http://www.nik-it.de}
 * @author     Created on 02-Oct-2009
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

ecrLoadHelper('pearhelpers.phpunit');

$phpUnit = new EasyPHPUnit;

if( ! $phpUnit->checkEnv()):
    echo 'Env check failed.. cannot continue :(';

    return;
endif;

$timeStamp = date('Ymd_his');

ecrHTML::initFileTree();

$jsFile = '';
$jsFile .= " onclick=\"doSelenium('[link]', '[file]', '$timeStamp', '[id]');\"";

$jsFolder = '';

$fileTree = new phpFileTree(JPATH_ROOT.DS.$this->testsBase, '', $jsFile, $jsFolder);
?>

<div class="ecr_floatbox">
    <strong><?php echo jgettext('Project'); ?></strong>
    <div class="ecrBigInfo">
	    <?php echo jgettext('Click a file to create a skeleton class.'); ?>
    </div>
    <?php echo drawFileTree($this->project); ?>
</div>

<div class="ecr_floatbox">
    <strong><?php echo jgettext('Tests'); ?></strong>
    <div class="ecrBigInfo">
	    <?php echo jgettext('Click a to execute a UnitTest.'); ?>
    </div>
    <div class="file_tree_path"><?php echo str_replace(DS, '&nbsp;'.DS.'&nbsp;', $this->testsBase); ?></div>
    <div id="test_tree">
        <?php echo $fileTree->drawFullTree(); ?>
    </div>
</div>

<div class="ecr_floatbox">
    <strong><?php echo jgettext('Options'); ?></strong>
    <br />
    <input type="checkbox" checked="checked" id="show_result" />
    <label for="show_result"><?php echo jgettext('Show results'); ?></label>
    <br />
    <input type="checkbox" checked="checked" id="show_console" />
    <label for="show_console" style="background-color: black; color: white;
    	font-family: monospace; padding: 0.1em;"><?php echo jgettext('Show console output'); ?></label>
    <p><b>@todo</b> add more options..</p>
</div>

<div class="ecr_floatbox">
    <div id="ecr_title_file"></div>
</div>

<div style="clear: both;"></div>

<input type="hidden" id="tests_base" value="<?php echo $this->testsBase; ?>" />
<input type="hidden" id="results_base" value="<?php echo $this->resultsBase; ?>" />

<div id="ecr_codeeye_output" style="padding-top: 0.2em;"><h2><?php echo jgettext('Output')?></h2></div>
<pre id="ecr_codeeye_console"><?php echo jgettext('Console'); ?></pre>

<?php

/**
 *
 * @param $project
 * @return unknown_type
 */
function drawFileTree($project)
{
    $ret = '';

    $file_path = JRequest::getString('file_path');
    $file_name = JRequest::getString('file_name');

    //--Allowed extensions
    //TODO set somewhere else...
    $allowed_exts = array('php', 'css', 'xml', 'js', 'ini', 'txt', 'html', 'sql');
    $allowed_pics = array('png', 'gif', 'jpg', 'ico');

    $javascript = '';
    $javascript .= " onclick=\"create_skeleton('[link]', '[file]', '[id]');\"";

    $jsFolder = '';
//    #$jsFolder .= " onmousedown=\"setPath('[link]/[file]');\"";
    $fileTree = new phpFileTree('', '', $javascript, $jsFolder);

    foreach($project->copies as $dir)
    {
        if(is_dir($dir))
        {
            $dspl = str_replace(JPATH_ROOT.DS, '', $dir);
            $dspl = str_replace(DS, ' '.DS.' ', $dspl);

            $ret .= '<div class="file_tree_path"><strong>JROOT</strong>'.BR.$dspl.'</div>';

            $fileTree->setDir($dir);
            $ret .= $fileTree->startTree();
            $ret .= $fileTree->drawTree();
            $ret .= $fileTree->endTree();
        }
        else if(JFile::exists($dir))
        {
            $show = true;

            foreach($project->copies as $test)
            {
                if(strpos($dir, $test))
                {
                    $show = false;
                }
            }//foreach

            if( ! $show)
            {
                continue;
            }

            //--This shows a single file not included in anterior directory list ;) - hi plugins...
            $fileName = JFile::getName(JPath::clean($dir));
            $dirName = substr($dir, 0, strlen($dir) - strlen($fileName));
            $oldDir =(isset($oldDir)) ? $oldDir : '';

            if($dirName != $oldDir)
            {
                $dspl = str_replace(JPATH_ROOT.DS, '', $dirName);
                $ret .= '<div class="file_tree_path"><strong>JROOT</strong>'.BR.$dspl.'</div>';
            }

            $oldDir = $dirName;

            if( ! isset($fileTree))
            {
                $fileTree = new phpFileTree($dir, "javascript:", $javascript);
            }
            else
            {
                $fileTree->setDir($dir);
            }

            $ret .= $fileTree->startTree();
            $ret .= $fileTree->getLink($dirName, $fileName);
            $ret .= $fileTree->endTree();

            $ret .= '<br />';
        }
    }//foreach

    return $ret;
}//function
