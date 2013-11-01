<?php

/*------------------------------------------------------------------------
# redim.php for PLG - SYSTEM - IMAGESIZER
# ------------------------------------------------------------------------
# author    reDim - Norbert Bayer
# copyright (C) 2011 redim.de. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.redim.de
# Technical Support:  Forum - http://www.redim.de/kontakt/
-------------------------------------------------------------------------*/

# reDim - InfoBox V1.0
// Check to ensure this file is within the rest of the framework
// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.html.html');
jimport('joomla.form.formfield');

class JFormFieldreDim extends JFormField
{
	/**
	* Element name
	*
	* @access	protected
	* @var		string
	*/
	public $type  = 'reDim';
	public $_version = '1.6';

	protected function ScanFolder($dir,$ext=0){
		$the_files=array();
	
		// check if directory exists
		if (is_dir($dir))
		{
			if ($handle = opendir($dir)) {
				while (false !== ($file = readdir($handle))) {
					if ($file != '.' && $file != '..' ) {
						$files[] = $dir .DS.$file;
					}
				}
			}
			closedir($handle);

			foreach ($files as $file)
			{
				if (is_dir($file)){
					if($ext==0){
				 	 	$file=str_replace(DS.DS,DS,$file);
						$ar=JFormFieldreDim::ScanFolder($file,0);
						if (is_array($ar)){
							$the_files=array_merge ($the_files,$ar);			 
						}												

					}
			 	}else{			 	 
			 	 	$file=str_replace(DS.DS,DS,$file);
					$the_files[] = $file;					
				}
			}
				
			
			unset($files);
		}		
			
			
		return $the_files;	
	}

	protected function getPluginFiles($dir="",$base=""){

		if($base==""){
			$base=$dir;
		}

		$files=JFormFieldreDim::ScanFolder($dir,$base);
		$html='<files>'."\n";
		foreach ($files as $file){
			$file=str_replace($base.DS,"",$file);
		 	$file="   <filename>".$file."</filename>";	 	 
			$html.= $file."\n";
		}
		$html.='</files>'."\n\n\n\n";
				
		$html='<textarea style="width:100%" rows="23" name="S1" cols="51">'.$html.'</textarea>';
		return $html;
	}


	protected function getInput()
	{
	#	$view =  $node->attributes('view');
		$view =  $this->element['view'];
		switch ($view){

		case 'pluginfiles':
			$html=JFormFieldreDim::getPluginFiles(JPATH_SITE.DS.$this->element['path']);
		break;
		
		case 'infomode':
		#	$img=JURI::root()."plugins/system/imagesizer/formfields/infomode.png";
		#	$html='<br style="clear: both" />'.JText::_("IMAGESIZER_INFO_MODE");
		#	$html.='<br /><img src="'.$img.'" />';
		#	$html.='<br style="clear: both" /><br />'.JText::_("IMAGESIZER_INFO_COMMAND");
			$html='<div>'.JText::_($this->element['description']).'</div>';
		break;


		case 'updatecheck':
			jimport( 'joomla.plugin.helper' );
			$dispatcher = JDispatcher::getInstance();
			$checkupdate = $dispatcher->trigger('onCheckupdate');

			if(@$checkupdate[0]==1){
				$eid= JRequest::getINT("extension_id",0);
				jimport('joomla.updater.update');
				$updater = JUpdater::getInstance();
				$results = $updater->findUpdates($eid, 86400);
		
				$db = & JFactory::getDBO();
				$db->setQuery("SELECT update_id  FROM #__updates AS a WHERE a.extension_id='".$eid."' LIMIT 1");
				$eid=$db->LoadResult();
	
				$key=JPATH_SITE.DIRECTORY_SEPARATOR.$this->element['path']."/redim_key.php";
	
				if(file_Exists($key)){
					include_once($key);
					if(defined("REDIM_DOWNLOADKEY")){
						$key=REDIM_DOWNLOADKEY;
					}else{
						$key="";
					}
				}else{
					$key="";
				}
	
		
				if($eid>0){
					jimport('joomla.updater.update');
					$updaterow = JTable::getInstance('update');
					$updaterow->load($eid);
	
					$html=JText::_("REDIM_IS_UPDATE").": ".$updaterow->name." ".$updaterow->version;
							
					$update = new JUpdate;
					$update->loadFromXML($updaterow->detailsurl);
					if(isset($update->downloadurl->_data)){
						$link= $update->downloadurl->_data;
						if(!empty($key)){
							$link.="?did=".$key;
						}
						$html= '<a title="'.JText::_("Download").'" href="'.$link.'">'.$html.'</a>';
					}
					
				}else{
					$html=JText::_("REDIM_NO_UPDATE");
	
				}
			}

		break;



		case 'logo':
		$html='<a href="http://www.redim.de" target=_blank><img src="http://www.redim.de/redim_j_logo.gif" border="0" width="198" height="67"></a><br style="clear:both"/>';
		break;


		case 'help':
            $html= JText::_("HELP1");
		break;

		}

		return $html;

	}
}