<?php
/**
 * @version SVN: $Id: ziper_folder.php 440 2011-07-17 01:39:26Z elkuku $
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath {@link http://www.nik-it.de}
 * @author     Created on 09-Jul-2011
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

$buildPath = $this->project->getZipPath();
?>
<strong class="img icon-16-installfolder"><?php echo jgettext('Build folder'); ?></strong>
<?php
echo JHTML::tooltip(jgettext('Build folder').'::'
.jgettext('The folder where your final package ends up. The folders extension_name and version will be added automatically.')
.jgettext('<br />If left blank the default folder will be used.'));

echo '<br /><br />';
//echo jgettext('Standard build folder');
//echo '<br />';
echo $buildPath;
?>
<!--
<br />
<br />
<label for="zipPath">
	<?php echo jgettext('Custom build folder'); ?>
</label>
<br />
<input type="text" id="zipPath" name="buildvars[zipPath]" size="40"
    value="<?php echo $this->project->zipPath; ?>" />
 -->
<?php
//echo DS.$this->project->comName;
echo DS.$this->project->version;

if( ! JFolder::exists($buildPath)) :
    ecrHTML::displayMessage(sprintf(jgettext('The folder %s does not exist'), $buildPath), 'error');
endif;
?>
