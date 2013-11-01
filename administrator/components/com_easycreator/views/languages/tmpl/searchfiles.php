<?php
/**
 * @version SVN: $Id: searchfiles.php 416 2011-06-12 18:31:43Z elkuku $
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath (elkuku) {@link http://www.nik-it.de NiK-IT.de}
 * @author     Created on 16-Apr-2009
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

if( ! $this->easyLanguage)
{
    ecrHTML::displayMessage(jgettext('No languages defined'), 'error');

    return;
}

JHTML::_('behavior.modal', 'a.modal');

$allFiles = array();
$languages = array_diff($this->languages, $this->hideLangs);
$lang_filter_file = JRequest::getVar('lang_filter_file', '');
$ecr_project = JRequest::getCmd('ecr_project');

if($this->scope != 'menu')
{
    foreach($this->definitions as $definition)
    {
        foreach($definition->files as $fName => $fCount)
        {
            if( ! in_array($fName, $allFiles))
            {
                $allFiles[] = $fName;
            }
        }//foreach
    }//foreach

    //TODO improve sorting.. on filenames only
    sort($allFiles);

    $searchTitle = jgettext('PHP XML file analysis');

    switch(ECR_JVERSION)
    {
        case '1.5':
            break;

        case '1.6':
        case '1.7':
            $searchTitle = jgettext('PHP, XML and JavaScript file analysis');
            break;

        default:
            ecrHTML::displayMessage(__METHOD__.' - Unknown J! version');
            break;
    }//switch

    echo '<h2>'.$searchTitle.' :: <span style="color: green;">'.ucfirst($this->scope).'</span></h2>';
}
else
{
    echo '<h2>'.jgettext('Menu analysis').'</h2>';
}

$baseLink = 'index.php?option=com_easycreator&amp;task=translate&amp;tmpl=component'
    .'&amp;view=languages&amp;controller=languages';

$baseLink .= '&amp;ecr_project='.$ecr_project;
$baseLink .= '&amp;scope='.$this->scope;
$baseLink .= '&amp;ret_type=phpxml';
$fieldID = 0;
$k = 0;

if($this->scope != 'menu') :
    $style =($lang_filter_file) ? ' style="color: red"' : ''; ?>
<div style="background-color: #ffffdd;"><span <?php echo $style; ?>><?php echo jgettext('Filter'); ?></span>
: <select name="lang_filter_file"
	onchange="submitbutton('searchfiles', 1)">
	<option value=""><?php echo jgettext('Select'); ?>...</option>
	<?php
    foreach($allFiles as $fName) :
        $parts = explode($ecr_project, $fName);
        $displayName = substr($fName, strpos($fName, $ecr_project) + strlen($ecr_project) + 1);
        $selected =($fName == $lang_filter_file) ? ' selected="selected"' : '';
        echo NL.'<option value="'.$fName.'"'.$selected.'>'.$displayName.'</option>';
    endforeach;
    ?>
</select></div>
<?php
endif;
?>

<table class="adminlist">
	<thead>
		<tr>
			<th style="background-color: #CCE5FF;"><?php echo jgettext('KEY'); ?></th>
			<th style="background-color: #FFFFB2;"><?php echo jgettext('Used in File'); ?>
			</th>
			<th>
			    <?php echo jgettext('Translated'); ?>
			    <span style="background-color: green; cursor: pointer;" class="hasEasyTip"
				title="<?php echo jgettext('Translated'); ?>">&nbsp;&nbsp;&nbsp;&nbsp;</span>
				<span style="background-color: red; cursor: pointer;"
				class="hasEasyTip" title="<?php echo jgettext('Not translated'); ?>">&nbsp;&nbsp;&nbsp;&nbsp;</span>
				<?php if($this->showCore) : ?>
    				<span style="background-color: orange; cursor: pointer;"
    				class="hasEasyTip"
    				title="<?php echo jgettext('Translated in core'); ?>">&nbsp;&nbsp;&nbsp;&nbsp;</span>
				<?php endif; ?>
			</th>
		</tr>
	</thead>
	<tbody>
	<?php

    foreach($this->definitions as $definition)
    {
        if($lang_filter_file
        && ! array_key_exists($lang_filter_file, $definition->files))
        {
            $k = 0;
            continue;
        }

        $coreCount = false;
        $def = strtoupper($definition->definition);
        $s = str_replace('"', '__QQ__', $def);
        $link = $baseLink.'&amp;trans_key='.$s;
        ?>
    <tr class="row<?php echo $k; ?>">
    	<td><span style="color: gray;"><?php echo $def; ?></span></td>
    	<td>
    	<?php
        foreach($definition->files as $fName => $fCount) :
            echo '<span style="background-color: #FFFFB2;">';
            echo substr($fName, strpos($fName, $ecr_project) + strlen($ecr_project) + 1).' ('.$fCount.')';
            echo '</span><br />';
        endforeach;
        ?>
    	</td>

		<td>
		<?php
        if($this->showCore)
        {
            foreach($languages as $lang)
            {
                if(array_key_exists($def, $this->coreStrings))
                {
                    echo '<span style="color: orange;">'.$lang.'</span>&nbsp;';
                    $coreCount = true;
                }
            }//foreach
        }

        if( ! array_key_exists($def, $this->strings))
        {
            if( ! $coreCount)
            {
                foreach($languages as $lang)
                {
                    $fieldID ++;
                    $tmpLink = $link.'&amp;trans_lang='.$lang;
                    $tmpLink .= '&amp;field_id='.$fieldID;

                    if($lang == 'en-GB')
                    {
                        $adIDs = '&amp;ad_ids=';

                        for($i = 1; $i < count($languages); $i ++)
                        {
                            $adIDs .= $fieldID + $i;
                            $adIDs .=($i < count($languages) - 1) ? ',' : '';
                        }//for

                        $tmpLink .= $adIDs;
                    }

                    $display =($lang != 'en-GB') ? 'display: none;' : '';
                    $txt =($lang != 'en-GB') ? $lang : jgettext('Not translated');
                    ?>
		            <a class="ecr_button modal"
		            style="color: red; <?php echo $display; ?>"
		            title="<?php echo jgettext('Click to translate'); ?>"
        			href="<?php echo $tmpLink; ?>"
        			rel="{handler: 'iframe', size: {x: 820, y: 310}}"
        			id="trfield_<?php echo $fieldID; ?>">
        				<?php echo $txt; ?>
        			</a>
        			<?php
                }//foreach
            }
        }
        else
        {
            foreach($languages as $lang)
            {
                $fieldID ++;
                $tmpLink = $link.'&amp;trans_lang='.$lang;
                $tmpLink .= '&amp;field_id='.$fieldID;
                $style =(array_key_exists($lang, $this->strings[$def])) ? 'green' : 'red';
                ?>
		        <a class="ecr_button modal" style="color: <?php echo $style; ?>"
		        title="<?php echo jgettext('Click to translate'); ?>"
			    href="<?php echo $tmpLink; ?>"
			    rel="{handler: 'iframe', size: {x: 920, y: 330}}"
			    id="trfield_<?php echo $fieldID; ?>">
			        <?php echo $lang; ?>
			    </a>
			    <?php
            }//foreach
        }
        ?>
    	</td>

	</tr>
	<?php
    $k = 1 - $k;
}//foreach
?>
</tbody>
</table>
