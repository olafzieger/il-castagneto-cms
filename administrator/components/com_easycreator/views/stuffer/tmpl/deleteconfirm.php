<?php
/**
 * @version SVN: $Id: deleteconfirm.php 434 2011-07-02 20:27:06Z elkuku $
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath (elkuku) {@link http://www.nik-it.de NiK-IT.de}
 * @author     Created on 10-Mar-2008
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

$skull = JURI::root().'administrator/components/com_easycreator/assets/images/skull.png';
?>
<div  style="text-align: center; padding: 1em;">
	<h1 style="color: red;">
		<span><?php echo jgettext('Attention'); ?> !</span></h1>
		<h1>
		<?php echo sprintf(jgettext('The project %s will be deleted'), $this->project->name); ?>
	</h1>

	<div class="ecr_deletebutton" align="center" onclick="submitbutton('delete_project')">
		<img src="<?php echo $skull; ?>" alt="<?php echo jgettext('Delete'); ?>" />
		<br />
		<?php echo sprintf(jgettext('Delete %s project file'), $this->project->name); ?>
	</div>
	<br/>
	<br />
	<div class="ecr_deletebutton" align="center" onclick="submitbutton('delete_project_full')">
		<img src="<?php echo $skull; ?>" alt="<?php echo jgettext('Delete'); ?>" />
		<img src="<?php echo $skull; ?>" alt="<?php echo jgettext('Delete'); ?>" />
		<img src="<?php echo $skull; ?>" alt="<?php echo jgettext('Delete'); ?>" />
		<br />
		<?php echo sprintf(jgettext('Delete the whole %s project including files and database'), $this->project->name); ?>
	</div>
</div>
