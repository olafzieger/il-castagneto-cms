<?php
/**
 * @version SVN: $Id: ziperresult.php 440 2011-07-17 01:39:26Z elkuku $
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath (elkuku) {@link http://www.nik-it.de NiK-IT.de}
 * @author     Created on 07-Mar-2008
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

?>

<div class="ecr_floatbox">
<?php
$result = $this->EasyZiper->create($this->project);
$errors = $this->EasyZiper->getErrors();
?>
<?php if($result && ! $errors): ?>
	<h1 class="img icon-16-check_ok" style="color: green; text-align: center;">
		<?php echo jgettext('Your ZIPfile has been created sucessfully'); ?>
	</h1>
<?php
    $this->EasyZiper->displayDownloadLink();
else:
    ecrHTML::displayMessage(jgettext('Your ZIPfile has NOT been created'), 'error');
    echo '<h2>'.jgettext('Errors').'</h2>';
    echo '<ul><li>'.implode('</li><li>', $errors).'</li></ul>';
endif;

?>
</div>
<div class="ecr_floatbox">
<?php
$old = $this->setLayout('common');
echo $this->loadTemplate('archive');
$this->setLayout($old);
?>
</div>

<?php if(in_array('logging', $this->buildopts)): ?>
	<div class="ecr_floatbox">
        <div class="ecr_codebox_header" style="font-size: 1.4em;" onclick="toggleDiv('ecr_logdisplay');">
            <?php echo jgettext('Log File'); ?>
        </div>
        <div id="ecr_logdisplay" style="display: none;">
            <?php echo $this->EasyZiper->printLog(); ?>
        </div>
    </div>
<?php endif; ?>

<div style="clear: both;"></div>
<?php
