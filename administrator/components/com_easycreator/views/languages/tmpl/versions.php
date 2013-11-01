<?php
/**
 * @version $Id: versions.php 416 2011-06-12 18:31:43Z elkuku $
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath (elkuku) {@link http://www.nik-it.de NiK-IT.de}
 * @author     Created on 25-Sep-2008
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die(';)');


echo '<h1>'.jgettext('Versions').'</h1>';

if( ! $this->sel_language)
{
    ecrHTML::displayMessage(jgettext('Please choose a language'));

    return;
}

if( ! count($this->versions))
{
    ecrHTML::displayMessage(jgettext('No versions found'), 'notice');

    return;
}

//-- Add css
JHTML::stylesheet('diff.css', 'administrator/components/com_easycreator/assets/css/');

//-- Add Javascript
ecrScript('versions');

if($this->selected_version)
{
    $this->easyLanguage->displayVersion($this->selected_version, $this->sel_language);
}
?>
<table class="adminlist">
  <tr>
    <th><?php echo jgettext('Version'); ?></th>
    <th><?php echo jgettext('Size'); ?></th>
    <th><?php echo jgettext('Date'); ?></th>
    <th><?php echo jgettext('Actions'); ?></th>
  </tr>
  <?php
  $k = 0;
    foreach($this->versions as $version) :
    ?>
  <tr class="row<?php echo $k; ?>">
    <td><?php echo $version->revNo; ?></td>
    <td><?php echo $version->size; ?></td>
    <td><?php echo $version->lastMod; ?></td>
    <td>
    <div class="ecr_button" onclick="showVersion('<?php echo $version->revNo; ?>');"><?php echo jgettext('Show'); ?></div>
    </td>
  </tr>
  <?php
    $k = 1 - $k;
    endforeach;
 ?>
</table>

<input type="hidden" name="selected_version" />
