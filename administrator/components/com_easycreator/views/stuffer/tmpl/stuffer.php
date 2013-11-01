<?php
/**
 * @version SVN: $Id: stuffer.php 416 2011-06-12 18:31:43Z elkuku $
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath (elkuku) {@link http://www.nik-it.de NiK-IT.de}
 * @author     Created on 07-Mar-2008
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

ecrLoadHelper('language');

//--Add CSS
ecrStylesheet('menu');
ecrStylesheet('stuffer');

$extProps['module'] = array('name' => 'comName', 'client' => 'scope', 'position' => 'position', 'ordering' => 'ordering');
$extProps['plugin'] = array('name' => 'comName', 'client' => 'scope', 'order' => 'ordering');

$js = array();

$js[] = "var definedProjects = {";

$komma1 = false;

foreach($extProps as $eName => $eProps) :
    if( ! isset($this->projectList[$eName])) continue;

    if($komma1) $js[] = '  , ';
    $komma1 = true;
    $js[] = "'".$eName."s' : {";

    $komma2 = false;

    $prs = array();

    foreach($this->projectList[$eName] as $ext) :

        $ps = array();
        foreach($eProps as $prop => $trans) :
            if($prop == 'client' && $ext->scope == 'frontend') $ext->scope = 'site';

            $ps[] = "'$prop' : '".$ext->$trans."'";
        endforeach;

        if($komma2) $js[] = '   , ';
        $komma2 = true;
        $js[] = "   '".$ext->name."' : {".implode(', ', $ps).' }';

    endforeach;
    $js[] = '    }';
endforeach;

$js[] = '};';

if($this->project->type == 'component'):
    //--The picture chooser
    $js[] = "window.addEvent('domready', function() {";
    $js[] = "drawPicChooser('', '".$this->project->menu['img']."');";

    $js[] = '//--Submenus added by PHP';


    if(isset($this->project->submenu)) :
        foreach($this->project->submenu as $submenu) :
            $js[] = "addSubmenu('{$submenu['text']}', '".$submenu['link']
                ."', '".$submenu['img']."', '".$submenu['ordering']
                ."', '{$submenu['menuid']}', '{$this->project->menu['menuid']}');";
        endforeach;
    endif;

    if('1.5' != ECR_JVERSION)
    {
        $js[] = "sortSubMenu = new Sortables('#divSubmenu', {
        clone: true
    	});";
    }

    $js[] = '//--Package modules added by PHP';

    foreach($this->project->modules as $module) :
        $js[] = "addPackageElement('module', '".$module->scope."', '"
            .$module->name."', '".$module->title."', '"
            .$module->position."', '".$module->ordering."');";
    endforeach;

    $js[] = '//--Package plugins added by PHP';

    foreach($this->project->plugins as $plugin) :
        $js[] = "addPackageElement('plugin', '".$plugin->scope."', '"
        .$plugin->name."', '".$plugin->title."', '', '".$plugin->ordering."');";
    endforeach;

    $js[] = '});';
    ?>
<?php endif;

JFactory::getDocument()->addScriptDeclaration(implode(NL, $js));

ecrScript('addelement');
ecrScript('menu');

?>
<!-- just for js.. -->
<input type="hidden" value="0" id="totalCopys" />
<input type="hidden" value="0" id="totalSubmenu" />
<input type="hidden" value="0" id="totalPackageElementsModules" />
<input type="hidden" value="0" id="totalPackageElementsPlugins" />


<!-- Info & credits -->
<?php echo $this->loadTemplate('info'); ?>
<?php echo $this->loadTemplate('credits'); ?>

<?php
    echo $this->loadTemplate('packing');
if($this->project->type == 'component'):
    echo $this->loadTemplate('menu');
    echo $this->loadTemplate('install');
    echo $this->loadTemplate('package');
    echo $this->loadTemplate('autocode');
    echo $this->loadTemplate('language');
endif;
?>
<?php if($this->project->type == 'package') echo $this->loadTemplate('packageelements'); ?>

<?php echo $this->loadTemplate('update');
/*
<input type="hidden" name="buildvars[method]" value="<?php echo $this->project->method; ?>" />
 */
 ?>
<div style="clear: both; height: 1em;"></div>

<?php
ECR_DEBUG ? ecrDebugger::varDump($this->project, '$this->project') : null;
