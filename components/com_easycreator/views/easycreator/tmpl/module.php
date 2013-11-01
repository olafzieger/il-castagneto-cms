<?php
/**
 * @version SVN: $Id: module.php 45 2010-11-11 23:20:22Z elkuku $
 * @package    EasyCreator
 * @subpackage Frontent
 * @author     Nikolai Plath (elkuku) {@link http://www.nik-it.de NiK-IT.de}
 * @author     Created on 24-Sep-2008
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

JHTML::_('behavior.tooltip');

$prName = substr($this->selectedProject, 4);
$module = JModuleHelper::getModule($prName);
$basePath = JPATH_ROOT.DS.'modules'.DS.$this->selectedProject;
$xmlfile = $basePath.DS.$this->selectedProject.'.xml';
$parameter = new JParameter($module->params, $xmlfile);

?>
<table width="100%">
  <tr valign="top">
    <td>
    	<?php easyHTML::projectSelector(); ?>
    </td>
    <td>
    	<div style="border: 1px solid red; padding: 5px;">
    	<?php
        echo JModuleHelper::renderModule($module); ?>
		</div>

		<h2><?php echo jgettext('Parameters'); ?></h2>
		<?php echo $parameter->render(); ?>

    </td>
  </tr>
</table>
