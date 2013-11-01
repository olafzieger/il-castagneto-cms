<?php
/*
// "CSS Gallery" Plugin for Joomla 1.7 - Version 1.3.5
// License: GNU General Public License version 2 or later; see LICENSE.txt
// Author: Andreas Berger - andreas_berger@bretteleben.de
// Copyright (C) 2011 Andreas Berger - http://www.bretteleben.de. All rights reserved.
// Project page and Demo at http://www.bretteleben.de
// ***Last update: 2011-10-28***
*/

defined( '_JEXEC' ) or die( 'Restricted access' );

// Import library dependencies
jimport('joomla.plugin.plugin');

class plgContentCssgallery extends JPlugin {

	public $myarticleid = '00';//substitute article id on frontpage

	public function onContentPrepare($context, &$article, &$params, $limitstart=0) {

		// checking
		if (!isset($article->text)||!preg_match("#{becssg}(.*?){/becssg}#s", $article->text) ) {
			return;
		}

		//paths
		$path_absolute = 	JPATH_SITE;
		$path_site = 			JURI :: base();
		if(substr($path_site, -1)=="/") $path_site = substr($path_site, 0, -1);
		$path_imgroot = 	'/images/'; 													// default image root folder //1.6
		$path_ctrls = 		'/images/vsig_buttons/'; 							// button folder
		$path_plugin = 		'/plugins/content/cssgallery/files/'; // path to plugin folder
		$folder_thumbs = 	'becssg_thumbs'; 														// thumbnail subfolder
		$folder_images = 	'becssg_images'; 														// image subfolder

		// import helper
    JLoader::import( 'cssgalleryhelper', dirname( __FILE__ ).'/files' );

//captions
		if (preg_match_all("#{becssg_c}(.*?){/becssg_c}#s", $article->text, $matches, PREG_PATTERN_ORDER) > 0) {
			foreach ($matches[0] as $match) {
				$_raw_cap_ = preg_replace("/{.+?}/", "", $match);
				$_raw_cap_exp_ = explode("|",$_raw_cap_);
				$cap1=($_raw_cap_exp_[1]&&trim($_raw_cap_exp_[1])!="")?(trim(plgContentCssgalleryHelper::beStrtolower($_raw_cap_exp_[1]))):("CAPDEFAULT");
				$cap2=($_raw_cap_exp_[2]&&trim($_raw_cap_exp_[2])!="")?(trim($_raw_cap_exp_[2])):("");
				$cap3=($_raw_cap_exp_[3]&&trim($_raw_cap_exp_[3])!="")?(trim($_raw_cap_exp_[3])):("");
				$caparray="cap_ar".$_raw_cap_exp_[0];
				if(!isset($$caparray)){$$caparray=array();};
				${$caparray}[$cap1]=array($cap2,$cap3);
				//remove the call
				$article->text = plgContentCssgalleryHelper::beReplaceCall("{becssg_c}".$_raw_cap_."{/becssg_c}",'', $article->text);
			}
		} 
//captions

//links
		if (preg_match_all("#{becssg_l}(.*?){/becssg_l}#s", $article->text, $matches, PREG_PATTERN_ORDER) > 0) {
			$vsig_captions=array();
			foreach ($matches[0] as $match) {
				$_raw_link_ = preg_replace("/{.+?}/", "", $match);
				$_raw_link_exp_ = explode("|",$_raw_link_);
				$_link1=($_raw_link_exp_[1]&&trim($_raw_link_exp_[1])!="")?(trim(plgContentCssgalleryHelper::beStrtolower($_raw_link_exp_[1]))):("LINKDEFAULT");
				$_link2=($_raw_link_exp_[2]&&trim($_raw_link_exp_[2])!="")?(trim($_raw_link_exp_[2])):("");
				$_link3=($_raw_link_exp_[3]&&trim($_raw_link_exp_[3])!="")?(trim($_raw_link_exp_[3])):($_link2);
				$_link4=($_raw_link_exp_[4]&&trim($_raw_link_exp_[4])!="")?(trim($_raw_link_exp_[4])):("_self");
				$_linkarray="_linkar".$_raw_link_exp_[0];
				if(!isset($$_linkarray)){$$_linkarray=array();};
				${$_linkarray}[$_link1]=array($_link2,$_link3,$_link4);
				//remove the call
				$article->text = plgContentCssgalleryHelper::beReplaceCall("{becssg_l}".$_raw_link_."{/becssg_l}",'', $article->text);
			}
		}
//links

//images
		if (preg_match_all("#{becssg}(.*?){/becssg}#s", $article->text, $matches, PREG_PATTERN_ORDER) > 0) {
			$csscount = -1;
			$document =& JFactory::getDocument();
			
			foreach ($matches[0] as $match) {
				$csscount++;
				//split string and check for overrides
				$becssg_code = preg_replace("/{.+?}/", "", $match);
				$becssg_raw = explode ("|", $becssg_code);
				$_images_dir_ = $becssg_raw[0];
				if(substr($_images_dir_,-1,1)!="/"&&$_images_dir_!=""){$_images_dir_=$_images_dir_."/";} //add trailing slash
				if(substr($_images_dir_,0,1)=="/"&&$_images_dir_!=""){$_images_dir_=substr($_images_dir_,1,strlen($_images_dir_)-1);} //remove leading slash
				$_images_dir_enc = implode("/", array_map("rawurlencode", explode("/", $_images_dir_))); //path urlencoded

				unset ($becssg_overrides);
				$becssg_overrides=array();
				if(count($becssg_raw)>=2){ //there are parameteroverrides
					for($i=1;$i<count($becssg_raw);$i++){
						$overr_temp=explode("=",$becssg_raw[$i]);
						if(count($overr_temp)>=2){
							$becssg_overrides[strtolower(trim($overr_temp[0]))]=trim($overr_temp[1]);
						}
					}
				}

				unset($images);
				$noimage = 0;
				//read and process the param for the image root
				$path_imgroot	= trim($this->params->get('imagepath', $path_imgroot));
				if(substr($path_imgroot, -1)!="/"){$path_imgroot=$path_imgroot."/";} //add trailing slash
				if(substr($path_imgroot,0,1)!="/"){$path_imgroot="/".$path_imgroot;} //add leading slash

				// read directory and check for images
				if ($dh = @opendir($path_absolute.$path_imgroot.$_images_dir_)) {
					while (($f = readdir($dh)) !== false) {
						if((substr(strtolower($f),-4) == '.jpg') || (substr(strtolower($f),-4) == '.gif') || (substr(strtolower($f),-4) == '.png')) {
							$noimage++;
							$images[] = array('filename' => $f, 'flastmod' => filemtime($path_absolute.$path_imgroot.$_images_dir_.$f)); 
						}
					}
					closedir($dh);
					//damn, found the folder but it is empty
					$html2="<br />CSS Gallery:<br />No images found in folder ".$path_absolute.$path_imgroot.$_images_dir_."<br />";
				}
				else {
					//you promised me a folder - where is it?
					$html2="<br />CSS Gallery:<br />Could not find folder ".$path_absolute.$path_imgroot.$_images_dir_."<br />";
				}

				if($noimage) {
					// read in parameters and overrides
					$_imwidth_			= (array_key_exists("width",$becssg_overrides)&&$becssg_overrides['width']!="")?($becssg_overrides['width']):($this->params->get('im_width', 400));
					$_imheight_			= (array_key_exists("height",$becssg_overrides)&&$becssg_overrides['height']!="")?($becssg_overrides['height']):($this->params->get('im_height', 300));
					$_imquality_		= (array_key_exists("iqual",$becssg_overrides)&&$becssg_overrides['iqual']!="")?($becssg_overrides['iqual']):($this->params->get('im_quality', 95));
					$_imkeep_		 		= (array_key_exists("icrop",$becssg_overrides)&&$becssg_overrides['icrop']!="")?($becssg_overrides['icrop']):($this->params->get('im_keep', 'keep'));
					$_throw_				= (array_key_exists("throw",$becssg_overrides)&&$becssg_overrides['throw']!="")?($becssg_overrides['throw']):($this->params->get('th_row', 4));
					$_tbquality_		= (array_key_exists("tqual",$becssg_overrides)&&$becssg_overrides['tqual']!="")?($becssg_overrides['tqual']):($this->params->get('th_quality', 80));
					$_thkeep_		 		= (array_key_exists("tcrop",$becssg_overrides)&&$becssg_overrides['tcrop']!="")?($becssg_overrides['tcrop']):($this->params->get('th_keep', 'keep'));
					$_thspace_ 			= (array_key_exists("space",$becssg_overrides)&&$becssg_overrides['space']!="")?($becssg_overrides['space']):($this->params->get('th_space', 5));
					$_im_preload_		= (array_key_exists("prld",$becssg_overrides)&&$becssg_overrides['prld']!="")?($becssg_overrides['prld']):($this->params->get('im_preload', 1));
					$_im_align_ 		= (array_key_exists("align",$becssg_overrides)&&$becssg_overrides['align']!="")?($becssg_overrides['align']):($this->params->get('im_align', 1));
					$_im_fixstart_	= (array_key_exists("fixstart",$becssg_overrides)&&$becssg_overrides['fixstart']!="")?($becssg_overrides['fixstart']):($this->params->get('im_fixstart', 1));
					$_cap_show_ 		= (array_key_exists("caps",$becssg_overrides)&&$becssg_overrides['caps']!="")?($becssg_overrides['caps']):($this->params->get('cap_show', 1));
					$_th_sort_			= (array_key_exists("sort",$becssg_overrides)&&$becssg_overrides['sort']!="")?($becssg_overrides['sort']):($this->params->get('th_sort', 0));
					$_link_use_ 		= (array_key_exists("links",$becssg_overrides)&&$becssg_overrides['links']!="")?($becssg_overrides['links']):($this->params->get('link_use', 1));

					//calculate
					$thumbwidth=intval(($_imwidth_-($_thspace_*($_throw_-1)))/$_throw_);
					$thumbheight=intval($thumbwidth*($_imheight_/$_imwidth_));
					$_imwidth_=$_thspace_*($_throw_-1)+$thumbwidth*$_throw_;

					//sort images
					$images = plgContentCssgalleryHelper::beSortImages($images,$_th_sort_);

					//create a unique identifier for the current gallery
					$identifier=(isset($article->id))?($article->id."_".$csscount):($this->myarticleid."_".$csscount);
					//set the var for the current array of captions
					$captions="cap_ar".$csscount;
					//set the var for the current array of links
					$cssglinks="_linkar".$csscount;

					//set path of thumbnail directory
					$thumbdir=$path_absolute.$path_imgroot.$_images_dir_.$folder_thumbs.'/';
					//check_existence_of/create thumbdirectory
					if(!is_dir($thumbdir)){plgContentCssgalleryHelper::beMakeFolder($thumbdir,'thumbnail');}

					//set path of image directory
					$imgdir=$path_absolute.$path_imgroot.$_images_dir_.$folder_images.'/';
					//check_existence_of/create imagedirectory
					if(!is_dir($imgdir)){plgContentCssgalleryHelper::beMakeFolder($imgdir,'image');}

					/*option to use an external stylesheet
					if($csscount<=0){
						$document->addCustomTag('<link href="'.$path_site.'/plugins/content/plugin_becssg/becssg.css" rel="stylesheet" type="text/css" />' );
					}
					*/

					//main div
					$html2 = "\n<div id='becssg_holder_".$identifier."'><a id='g_".$identifier."'></a>\n";
					$html2 .= "<div id='becssg_main_".$identifier."'>\n";

					//preload-div
					if($_im_preload_){
						$html3 = "\n<div id='becssg_pre_".$identifier."'>\n";
					}

					//initiate arrays for css
					$thecss=array();
					$thetopcss=array();

					for($a=0;$a<$noimage;$a++) {
						if($images[$a]['filename'] != '') {
							//check_existence_of/create thumb
							$thethumb = plgContentCssgalleryHelper::beResizeImg($path_absolute.$path_imgroot.$_images_dir_.$images[$a]['filename'],$folder_thumbs,$thumbwidth,$thumbheight,$_thkeep_,'no',$_tbquality_);
							//check_existence_of/create image
							$theimage = plgContentCssgalleryHelper::beResizeImg($path_absolute.$path_imgroot.$_images_dir_.$images[$a]['filename'],$folder_images,$_imwidth_,$_imheight_,$_imkeep_,'no',$_imquality_);

							//prepare captions
							$capstoshow="";
							unset($currentarray);
							$alttext=htmlspecialchars(utf8_encode(substr($images[$a]['filename'], 0, -4)), ENT_QUOTES);
							if(isset($$captions)){
									if(array_key_exists(plgContentCssgalleryHelper::beStrtolower($images[$a]['filename']),$$captions)){$currentarray=${$captions}[plgContentCssgalleryHelper::beStrtolower($images[$a]['filename'])];$alttext=htmlspecialchars($currentarray[0], ENT_QUOTES);}
									elseif(array_key_exists("CAPDEFAULT",$$captions)){$currentarray=${$captions}["CAPDEFAULT"];$alttext=htmlspecialchars($currentarray[0], ENT_QUOTES);}
									else{$currentarray=array("","");}
								if($_cap_show_&&($currentarray[0]!=""||$currentarray[1]!="")){
									$capstoshow="<span>";
									$capstoshow.=($currentarray[0]!="")?("<span class='becssg_cap_title'>".$currentarray[0]."</span>"):("");
									$capstoshow.=($currentarray[1]!="")?("<span>".$currentarray[1]."</span>"):("");
									$capstoshow.="</span>";
								}
							}

							//prepare link
							if(isset($currentlink)){unset($currentlink);};
							$currentlink=array("#g_".$identifier,$alttext,"_self");
							if($_link_use_&&isset($$cssglinks)){
								if(array_key_exists(plgContentCssgalleryHelper::beStrtolower($images[$a]['filename']),$$cssglinks)){$currentlink=${$cssglinks}[plgContentCssgalleryHelper::beStrtolower($images[$a]['filename'])];$alttext=htmlspecialchars(${$cssglinks}[plgContentCssgalleryHelper::beStrtolower($images[$a]['filename'])][1], ENT_QUOTES);}
								elseif(array_key_exists("LINKDEFAULT",$$cssglinks)){$currentlink=${$cssglinks}["LINKDEFAULT"];}
							}

							//write thumb
							$html2 .= "<img src='".$path_site.$path_imgroot.$_images_dir_enc.$folder_thumbs.'/'.$thethumb[1]."' alt='".$currentlink[1]."' title='".$currentlink[1]."' class='i_".$identifier."_".$a."'/><a href='".$currentlink[0]."' class='l_".$identifier."_".$a." i_".$identifier."_".$a." mylink_".$identifier." mylink' title='".$currentlink[1]."' target='".$currentlink[2]."'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$capstoshow."</a>\n";
							//write preload-img
							if($_im_preload_){
								$html3 .="<img src='".$path_site.$path_imgroot.$_images_dir_enc.$folder_images.'/'.$theimage[1]."' alt='".$currentlink[1]."' />\n";
							}
						
							//fed css-array
							$thumbrow=intval(($a)/$_throw_);
							$thumbrowpos=$a%$_throw_;
							$thumbleft=intval(($thumbwidth+$_thspace_)*$thumbrowpos+($thumbwidth-$thethumb[3])/2);
							$thumbtop=intval($_imheight_+($_thspace_+$thumbheight)*($thumbrow+1)-$thethumb[4]);
							$capbottom=intval(($_thspace_+$thumbheight)*intval(($noimage-1)/$_throw_+1));
							$backgroundleft=intval(($_imwidth_-$theimage[3])/2);
							$backgroundtop=intval(($_imheight_-$theimage[4])/2);
							$thecss[]=".i_".$identifier."_".$a." {font-size:".$thumbheight."px;line-height:".$thumbheight."px;position:absolute;left:".$thumbleft."px;top:".$thumbtop."px;width:".$thethumb[3]."px;height:".$thethumb[4]."px;}";
//						$thecss[]=".l_".$identifier."_".$a." {}";
							$thecss[]=".l_".$identifier."_".$a.":hover {background-image:url(".$path_site.$path_imgroot.$_images_dir_enc.$folder_images.'/'.$theimage[1].") !important;background-position:".$backgroundleft."px ".$backgroundtop."px !important;}";
							//css for top image
							if($a==0) {
								$thetopcss[]=$theimage[1];
								$thetopcss[]=$backgroundleft;
								$thetopcss[]=$backgroundtop;
							}
						}
					}
					//calculate gallerheight
					$galleryheight=intval($_imheight_+($thumbrow+1)*($_thspace_+$thumbheight));

					//prepare caption for main image
					if($_cap_show_&&isset($$captions)){
						if(array_key_exists(plgContentCssgalleryHelper::beStrtolower($images[0]['filename']),$$captions)){$currentarray=${$captions}[plgContentCssgalleryHelper::beStrtolower($images[0]['filename'])];}
						elseif(array_key_exists("CAPDEFAULT",$$captions)){$currentarray=${$captions}["CAPDEFAULT"];}
						else{$currentarray=array("","");}
						if($_cap_show_&&($currentarray[0]!=""||$currentarray[1]!="")){
							$html2.="<span id='becssg_cap_".$identifier."'>";
							$html2.=($currentarray[0]!="")?("<span class='becssg_cap_title'>".$currentarray[0]."</span>"):("");
							$html2.=($currentarray[1]!="")?("<span>".$currentarray[1]."</span>"):("");
							$html2.="</span>";
						}
					}
					$html2 .="</div>\n</div>\n";
					//preload
					if($_im_preload_){
						$html3 .="</div>\n";
						$html2 .=$html3;
					}

					$csstoinsert="<style type='text/css'>\n";
					//holder
					$csstoinsert.="#becssg_holder_".$identifier." {width:".$_imwidth_."px;height:".$galleryheight."px;";
					if($_im_align_==0){$csstoinsert.="margin:0 0 0 auto;padding:0;display:block;";}
					elseif($_im_align_==1){$csstoinsert.="margin:auto;padding:0;display:block;";}
					elseif($_im_align_==3){$csstoinsert.="margin:10px;float:left;";}
					elseif($_im_align_==4){$csstoinsert.="margin:10px;float:right;";}
					$csstoinsert.="}\n";
	
					$csstoinsert.="#becssg_main_".$identifier." {display:block;position:absolute;width:".$_imwidth_."px;height:".$galleryheight."px;background-image:url(".$path_site.$path_imgroot.$_images_dir_enc.$folder_images.'/'.$thetopcss[0].");background-position:".$thetopcss[1]."px ".$thetopcss[2]."px;background-repeat:no-repeat;}\n";
					$csstoinsert.="#becssg_main_".$identifier." img {margin:0 0 0 0 !important;}\n";
					if(!$_im_fixstart_){
					$csstoinsert.="#becssg_main_".$identifier.":hover {background-image:url('');}\n";
					}
					$csstoinsert.="#becssg_main_".$identifier." a:active, #becssg_main_".$identifier." a:focus {background-color:transparent !important;background-image:url('');}\n";
					$csstoinsert.="a.mylink_".$identifier." {text-decoration:none !important;overflow:hidden;z-index:5;}\n";
					$csstoinsert.="a.mylink_".$identifier.":hover {text-decoration:none !important;position:absolute;left:0;top:0;width:".$_imwidth_."px;height:".$galleryheight."px;background-repeat:no-repeat !important;background-color:transparent !important;z-index:2;}\n";
					if($_cap_show_&&isset($$captions)){
					$csstoinsert.=".mylink_".$identifier.":hover span {position:absolute;left:0;bottom:".$capbottom."px;}\n";
					$csstoinsert.=".mylink span {visibility:hidden;vertical-align:bottom;line-height:0;font-size:0;margin:0;padding:0;}\n";
					$csstoinsert.=".mylink:hover span {width:100%;display:block;visibility:visible;z-index:3;background:#fff;line-height:0;font-size:0;filter:alpha(opacity=60);opacity:0.60;border:none;}\n";
					$csstoinsert.=".mylink:hover span span {position:relative;top:0;left:0;line-height:12px;font-size:12px;margin:3px;display:block;font-weight:normal;color:#000;background:transparent;filter:alpha(opacity=100);opacity:1;border:none;}\n";
					$csstoinsert.=".mylink:hover span span.becssg_cap_title {font-weight:bold;display:block;}\n";
	
					$csstoinsert.="#becssg_cap_".$identifier." {position:absolute;left:0;bottom:".$capbottom."px;width:100%;vertical-align:bottom;display:block;visibility:visible;background:#fff;line-height:0;font-size:0;filter:alpha(opacity=60);opacity:0.60;border:none;}\n";
					if(!$_im_fixstart_){
					$csstoinsert.="#becssg_main_".$identifier.":hover > span {visibility:hidden;}\n";
					}
					$csstoinsert.="#becssg_cap_".$identifier." span {position:relative;top:0;left:0;line-height:12px;font-size:12px;margin:3px;display:block;font-weight:bold;color:#000;background:transparent;filter:alpha(opacity=100);opacity:1;border:none;}\n";
					$csstoinsert.="#becssg_cap_".$identifier." span + span {font-weight:normal;display:block;}\n";
					}
					//preload-css
					if($_im_preload_){
						$csstoinsert.="#becssg_pre_".$identifier." {position:absolute;top:-1000px;left:-1000px;width:5px;height:5px;overflow:hidden;}\n";
						$csstoinsert.="#becssg_pre_".$identifier." img {position:absolute;top:0;left:0;}\n";
					}
					$csstoinsert.="\n";
					for($i=0;$i<=count($thecss)-1;$i++){
						$csstoinsert.=trim($thecss[$i])."\n";
					}
					$csstoinsert.="</style>\n";
					$document->addCustomTag($csstoinsert);
				}
				//replace the call with the gallery
				$article->text = plgContentCssgalleryHelper::beReplaceCall("{becssg}".$becssg_code."{/becssg}",$html2, $article->text);
			}
		}
//images
	}
	public function onContentAfterDisplay($context, &$article, &$params, $limitstart=0) {
		if(isset($article->id)){$this->myarticleid=$article->id;} //substitute article id on frontpage
	}
}
?>