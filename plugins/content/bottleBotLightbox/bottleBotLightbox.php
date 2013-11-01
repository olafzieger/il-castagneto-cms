<?php
/**
 * BottleBot Lightbox for Joomla! 1.6+
 * @license http://www.gnu.org/licenses/gpl.html GNU/GPL.
 * @by bottlebot (thanks to Protos for Protos.Lightbox 1.1, which was used as a learning and template base)
 * @Copyright (C) 2011 BottleBot
 */

// no direct access

defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

class plgContentBottlebotLightBox extends JPlugin
{	
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);
		
		$bbVersion='BottleBot Lightbox J1.7+J1.6 v1.5';
		$bbLang=& JFactory::getLanguage();
		$bbLangStr = $bbLang->getTag();
		
		if ($this->params->get('forceEngLang'))
		{
			$bbLangStr = "en";
		}
		
		$bbHead = '';
		
		//---------------------------------------Load Custom Header---------------------------------------

		if ($this->params->get('useCustom'))
		{
			$bbHead = '<!-- Custom Lightbox Start (' . $bbVersion . ')-->
				<script type="text/javascript" src="' . JURI::base() . $this->params->get('customJS') . '"></script>
				<link rel="stylesheet" href="' . JURI::base() . $this->params->get('customCSS') . '" type="text/css" />
				<!-- Custom Lightbox End -->';
		}
		else 
		{
			
		//---------------------------------------Load Shadowbox Header------------------------------------
			
			if ($this->params->get('lbType')=='Shadowbox') 
			{	
				if ($this->params->get('enableDiashow'))
				{
					$bbDiaDelay=$this->params->get('diaDelay');
				}
				else
				{
					$bbDiaDelay=0;
				}
				
				$bbHead = '<!-- ShadowBox 3.0.3 Start (' . $bbVersion . ')-->
				
				<link rel="stylesheet" type="text/css" href="' . JURI::base().'plugins/content/bottleBotLightbox/sb/shadowbox.css">
				<script type="text/javascript">
					var bbPlayerExt=["flv","m4v","mp4", "mov"],
					bbPlayerPath="' . JURI::base() . 'plugins/content/bottleBotLightbox/sb/player.swf",
					bbExpressInstallPath="' . JURI::base() . 'plugins/content/bottleBotLightbox/sb/expressInstall.swf";
				</script>
				<script type="text/javascript" src="' . JURI::base() . $this->getValidLangPath($bbLangStr, 'plugins/content/bottleBotLightbox/sb/shadowbox-bbBBLANGbb.js') . '"></script>
				<script type="text/javascript">
				Shadowbox.init({
					slideshowDelay: ' . $bbDiaDelay . ',
					continuous: true,
					counterType:\'skip\',
					animSequence:\'sync\',
					flashVars: { \'autostart\' : ' . $this->params->get('enableAutoplay') . '}
				});
				</script>
				
				<!-- ShadowBox End -->';
			}
			
			//Shadowbox.loadLanguage(\'' . $bbLangStr . '\', \'' . JURI::base().'plugins/content/bottleBotLightbox/sb/src/lang\');
			
		//---------------------------------------Load Slimbox 2.04 Header------------------------------------
			
			else if ($this->params->get('lbType')=='Slimbox')
			{
				$bbHead = '<!-- Slimbox 2.04 (' . $bbVersion . ')-->
				
				<link rel="stylesheet" href="' . JURI::base() . $this->getValidLangPath($bbLangStr, 'plugins/content/bottleBotLightbox/slb/css/bbBBLANGbb_slimbox2_' . $this->params->get('lbColour') . '.css') . '" type="text/css" media="screen" />' .
				$this->checkIfTrue($this->params->get('enableJQuery'), '<script type="text/javascript" src="' . JURI::base() . 'plugins/content/bottleBotLightbox/fb/jquery.min.js"></script>') . '
				<script type="text/javascript" src="' . JURI::base() . $this->getValidLangPath($bbLangStr, 'plugins/content/bottleBotLightbox/slb/js/bbBBLANGbb_slimbox2.js') . '"></script>
				
				<!-- Slimbox -->';
			}
			
		//---------------------------------------Load FancyBox Header------------------------------------
			
			else if ($this->params->get('lbType')=='FancyBox')
			{
				$bbHead = '<!-- FancyBox Start (' . $bbVersion . ')-->' . 
				
				$this->checkIfTrue($this->params->get('enableJQuery'), '<script type="text/javascript" src="' . JURI::base().'plugins/content/bottleBotLightbox/fb/jquery.min.js"></script>') . '	
				<script type="text/javascript" src="' . JURI::base().'plugins/content/bottleBotLightbox/fb/jquery.fancybox-1.3.4.pack.js"></script>
				<script type="text/javascript" src="' . JURI::base().'plugins/content/bottleBotLightbox/fb/jquery.mousewheel-3.0.4.pack.js"></script>
				<link rel="stylesheet" href="' . JURI::base().'plugins/content/bottleBotLightbox/fb/jquery.fancybox-1.3.4.css" type="text/css" media="screen" />
					
				<!-- FancyBox End -->';
			}
		}
		
		$doc =& JFactory::getDocument();
		$doc->addCustomTag($bbHead);
	}
	
	public function onContentPrepare($context, &$article, &$params, $page = 0)
	{
		$gTmbPrefix = $this->params->def('tmbPrefix', '');
		
		$regex = "#<img[^<^>]*?src\s*?=\s*?['\"][^<^>]*?" . $gTmbPrefix . "[^<^>]*?\.(jpe?g|gif|png|bmp)['\"][^<^>]*?/>#i";
		$matches	= array();
		$count = 0;
		
		if (preg_match_all($regex, $article->text, $matches, PREG_SET_ORDER) >= 1)
		{
			$article->text = $this->bottlebotlightbox_replacer($article->text, $matches, $count);
		}
	}
	
	protected function bottlebotlightbox_replacer ($input,  &$matches) 
	{
		$bbDebug = 0;

		if (isset($_GET['bottlebotdebug']))
		{
			$bbDebug = $_GET['bottlebotdebug'];
		}
		
		$bbFLVHDEnabled=$this->params->def('vidHDEnabled', '');
		
		$newOutput=$input;
		$i = 0;
		
		foreach ($matches as $myMatch)
		{
			if (@$myMatch[0])
			{       
				
				if ($bbDebug)
				{
					echo "<br>-------------";
					echo "<br>myMatch[0]: " . htmlentities($myMatch[0]);
				}
				
				// Gallery Handling
				
				$imgGalleryMatches = array();
				$gGallery="";
				$gSavedGallery="";
				$bbVidWidth='';
				$bbVidHeight='';
				$bbFLVWidth='';
				$bbFLVHeight='';
				$bbVidLocalFile=0;
				$gAddLBParams = '';
				
		        preg_match( "@alt\s*=\s*['\"]\[bbgal=(.*?)\].*?['\"]@i", $myMatch[0], $imgGalleryMatches );
		        if (isset($imgGalleryMatches[1]))
		        {
		        	if ($bbDebug)
					{
						echo "<br>Gallery: " . $imgGalleryMatches[1];
					}	
					
		        	$gGallery=$imgGalleryMatches[1];
		        }
		        
		        if (trim($gGallery)=="")
				{
						$gGallery="";
						$gSavedGallery=$gGallery;
				}
				else 
				{
					$gSavedGallery="[bbgal=" . $gGallery . "]";
					
					if ($bbDebug)
					{
						echo "<br>SavedGallery: " .$gSavedGallery;
					}
					
					if ($this->params->get('lbType')=='FancyBox')
					{
						$gGallery=$gGallery;
					}
					else if ($this->params->get('lbType')=='Slimbox')
					{
						$gGallery="-" . $gGallery;
					}
					else
					{
						$gGallery="[" . $gGallery . "]";
					}
					
					if ($bbDebug)
					{
						echo "<br>GalleryCode: " .$gGallery;
					}
				}
				
				// #################################### FILETYPE HANDLING ####################################
				
				$bbFileType="";
				$gSavedFileType="";
				$bbVidLocalFile=0;
				
				$possibleFileTypes = array(	0 => "swf",
											1 => "flv",
											2 => "mov",
											3 => "mp4",
											4 => "m4v"
											);
	
				$fileTypeMatches = array();
				
				for ($bbFileTypeNum=0; $bbFileTypeNum<count($possibleFileTypes); $bbFileTypeNum++)
				{
					preg_match( "@alt\s*=\s*['\"].*?\[bbvid=(.*?\." . $possibleFileTypes[$bbFileTypeNum] . ".*?)\].*?['\"]@i", $myMatch[0], $fileTypeMatches);
					if (isset($fileTypeMatches[1]))
					{
						$bbFileType=$possibleFileTypes[$bbFileTypeNum];
	
						$bbFileTypeVal=$fileTypeMatches[1];
						
						$gSavedFileType='[bbvid=' . $bbFileTypeVal . ']';
						
						break;
					}
				}
		
				if ($bbFileType=="") // Check for YouTube e.g.
				{
					$possibleProvider = array(	0 => "www.youtube.com",
											1 => "www.myvideo.de"
											);
											
					$providerMatches = array();
											
					for ($bbProviderNum=0; $bbProviderNum<count($possibleProvider); $bbProviderNum++)
					{
						preg_match( "@alt\s*=\s*['\"].*?\[bbvid=(.*?" . $possibleProvider[$bbProviderNum] . ".*?)\].*?['\"]@i", $myMatch[0], $providerMatches);
						if (isset($providerMatches[1]))
						{						
							switch ($bbProviderNum) {
								case 0: $bbFileType="yt"; break;
								case 1: $bbFileType="mv"; break;
								default: break;
							}
							
							$gSavedFileType='[bbvid=' . $providerMatches[1] . ']';
							
							$bbFileTypeVal=$providerMatches[1];
							
							$bbFlashProvider="";
							
							if ($bbFileType=="yt")
							{
								$bbFlashProvider="http://www.youtube.com/v/";
								preg_match("/v=(\w+)/", $bbFileTypeVal, $bbVidMatches);
								$bbFileTypeVal = $bbFlashProvider . $bbVidMatches[1] . '&fs=1';
							}
							
							if ($bbFileType=="mv")
							{
								$bbFlashProvider="http://www.myvideo.de/movie/";
								preg_match("@watch/(\w+)@", $bbFileTypeVal, $bbVidMatches);
								$bbFileTypeVal = $bbFlashProvider . $bbVidMatches[1] . '&fs=1';
							}
							
							if ($this->params->get('useVidIdOnly'))
							{	
								$bbFileTypeVal=$bbFlashProvider . $bbFileTypeVal . '&fs=1';
							}
							
							break;
						}
					}
				}
	
				if ($bbDebug)
				{
					echo "<br>bbFileType: " .$bbFileType;
				}
	
				if ($bbFileType=="")
				{
					// Check for local video-file
					
					$localFileMatches = array();
					
					preg_match( "@alt\s*=\s*['\"].*?\[bbvid=(auto.*?)\].*?['\"]@i", $myMatch[0], $localFileMatches);
					if (isset($localFileMatches[1]))
					{
						$gSavedFileType=$localFileMatches[1];
						$bbVidLocalFile=1;
					}
				}
				
				if ($bbDebug)
				{
					echo "<br>bbVidLocalFile: " .$bbVidLocalFile;
				}
				
				// Check for bbw and bbh
				if (($bbFileType!="") || $bbVidLocalFile)
				{	
					$vidWidthMatches = array();
					preg_match( "@,bbw=(\w+)@i", $gSavedFileType, $vidWidthMatches);
					
					if (isset($vidWidthMatches[1]))
					{						
						$bbVidManualDimension=1;
						$bbVidWidth=$vidWidthMatches[1];
						
						$vidHeightMatches = array();
						preg_match( "@,bbh=(\w+)@i", $gSavedFileType, $vidHeightMatches);
						$bbFileTypeVal=str_replace(',bbw=' . $bbVidWidth, '', $bbFileTypeVal);
						
						if (isset($vidHeightMatches[1]))
						{
							$bbVidHeight=$vidHeightMatches[1];
								
							$bbFileTypeVal=str_replace(',bbh=' . $bbVidHeight, '', $bbFileTypeVal);
						}	
					}
					else
					{
						$bbVidManualDimension=0;
					}	
				}
				
				// #################################### PARAMS ####################################
		        
		        $gImgSrc=$this->getImgAtt('src', $myMatch[0]);   
		        $gImgTitle= $this->getImgAtt('title', $myMatch[0]);   
		        $gImgAlign=$this->getImgAtt('align', $myMatch[0]);
		        $gImgBorder=$this->getImgAtt('border', $myMatch[0]);
				$gImgHspace=$this->getImgAtt('hspace', $myMatch[0]);
				$gImgVspace=$this->getImgAtt('vspace', $myMatch[0]);
				$gImgId=$this->getImgAtt('id', $myMatch[0]);
				$gImgName=$this->getImgAtt('name', $myMatch[0]);
				$gImgUsemap=$this->getImgAtt('usemap', $myMatch[0]);
				$gImgDir=$this->getImgAtt('dir', $myMatch[0]);
				$gImgOnClick=$this->getImgAtt('onclick', $myMatch[0]);
				$gImgOnMouseOut=$this->getImgAtt('onmouseout', $myMatch[0]);
				$gImgOnMouseOver=$this->getImgAtt('onmouseover', $myMatch[0]);
				$gImgStyle=$this->getImgAtt('style', $myMatch[0]);
			
				// ######################### Remove Options-Text from alt-Tag #######################
				
				$gImgDesc = str_replace($gSavedGallery, "", $this->getImgAtt('alt', $myMatch[0]));
				$gImgDesc = str_replace($gSavedFileType, "", $gImgDesc);	
			}
			
			$gparts=explode("/", $gImgSrc);
		
			$gfilename=$gparts[count($gparts)-1];
			$gTmbName=$gfilename;
			$gfolder='';
			
			for ($t=0; $t<(count($gparts)-1); $t++)
			{
				$gfolder.=$gparts[$t] . '/';
			}
	
			if ($this->params->get('enableTmbRescale'))
			{
				$gWidth=$this->params->get('width');
				$gHeight=$this->params->get('height');
			}
			else
			{
				$gWidth=$this->getImgAtt('width', $myMatch[0]);
				$gHeight=$this->getImgAtt('height', $myMatch[0]);
			}
			
			$rawImg='<img ' . $this->bottlebotlightbox_checkAttIfEmpty('alt', $gImgDesc) .'src="'.$this->params->get('folder') . $gfolder . $gTmbName . '" ' . $this->bottlebotlightbox_checkAttIfEmpty('title', $gImgTitle) . $this->bottlebotlightbox_checkAttIfEmpty('height', $gHeight) . $this->bottlebotlightbox_checkAttIfEmpty('width', $gWidth) . $this->bottlebotlightbox_checkAttIfEmpty('class', $this->params->get('tmbClass')) . $this->bottlebotlightbox_checkAttIfEmpty('align', $gImgAlign) . $this->bottlebotlightbox_checkAttIfEmpty('border', $gImgBorder) . $this->bottlebotlightbox_checkAttIfEmpty('hspace', $gImgHspace) . $this->bottlebotlightbox_checkAttIfEmpty('vspace', $gImgVspace) . $this->bottlebotlightbox_checkAttIfEmpty('id', $gImgId) . $this->bottlebotlightbox_checkAttIfEmpty('style', $gImgStyle) . $this->bottlebotlightbox_checkAttIfEmpty('name', $gImgName) . $this->bottlebotlightbox_checkAttIfEmpty('usemap', $gImgUsemap) . $this->bottlebotlightbox_checkAttIfEmpty('dir', $gImgDir) . $this->bottlebotlightbox_checkAttIfEmpty('onClick', $gImgOnClick) . $this->bottlebotlightbox_checkAttIfEmpty('onMouseOver', $gImgOnMouseOver) . $this->bottlebotlightbox_checkAttIfEmpty('onMouseOut', $gImgOnMouseOut) . '/>';
	
			// ######################### Check, if big Image exists ##################################
		
			$gBigName=str_replace($this->params->get('tmbPrefix'), "", $gTmbName);
			
			if ($bbDebug==1)
			{
				echo '<br>Thumbnail:' . $this->params->get('folder') . $gfolder . $gTmbName ;
				echo '<br>Bildname:' . $this->params->get('folder') . $gfolder . $gBigName;
			}
			
			if ($bbVidLocalFile)  // if bbvid is set to "auto"
			{
				for ($p=0; $p<count($possibleFileTypes); $p++)
				{
					$localFileParts=explode('.', $gBigName);
	
					if (file_exists($this->params->get('folder') . $gfolder . $localFileParts[0] . '.' . $possibleFileTypes[$p]))
					{
						$bbFileType=$possibleFileTypes[$p];
						$bbFileTypeVal=$this->params->get('folder') . $gfolder . $localFileParts[0] . '.' . $possibleFileTypes[$p];
						
						break;
					}
				}	
			}
			
			if (((!file_exists($this->params->get('folder') . $gfolder . $gBigName)) || ($gTmbName==$gBigName)) && $bbFileType=="")
			{
				if ($bbDebug==1)
				{
					echo '<font color="blue">Datei ' . $this->params->get('folder') . $gfolder . $gBigName . '" existiert nicht.</font><br>';
				}
				
				if ($this->params->get('debugFileName'))
				{
					$gImgStyle='border: 1px solid #FF0000; width: ' . $gWidth. 'px;';
					$limage='<div style="font-size:10px; color:#FF0000; font-family:verdana; width:' . $gWidth . 'px;">Datei "' . $this->params->get('folder') . $gfolder . $gBigName . '" existiert nicht.' . $rawImg . '</div>';
				}
				else 
				{
					$limage=$rawImg;
				}
			}
			else
			{
				if ($this->params->get('useCustom'))
				{
					$limage= '<a href="'.$this->params->get('folder') . $gfolder . $gBigName . '" rel="' . $this->params->get('customRel') . $this->params->get('lightboxparameter') . '" class="' . $this->params->get('lnkClass') . '" title="' . $gImgTitle .'" onFocus="if(this.blur)this.blur()">' . $rawImg . '</a>';
				}
				else 
				{
					if ($this->params->get('lbType')=='Shadowbox')
					{
						//$gAddLBParams=';options={counterType:\'skip\',continuous:true,animSequence:\'sync\''  . $gDiaParams . $this->params->get('lightboxparameter') .'}';
					}
					else 
					{
						$gAddLBParams=$this->params->get('lightboxparameter');
					}
				}	
				
				// ########################## Setting the basic Anchor-Values ################################## 
				
				$bbRelAttrib='lightbox' . $gGallery;
				
				if ($this->params->get('lbType')=='FancyBox')
				{
					$bbRelAttrib=$gGallery;
				}
				
				if (($bbFileType!="") && ($this->params->get('lbType')!='Slimbox'))		// Video
				{
					// HD Filehandling	
					$bbFLVUrlHD='';
							
					if ($bbFLVHDEnabled)
					{	
						$bbRawFLVUrl=str_replace('.' . $bbFileType, '', $bbFileTypeVal);
								
						for ($hdFileTypeNum=0; $hdFileTypeNum<count($possibleFileTypes); $hdFileTypeNum++)
						{
							if (file_exists($bbRawFLVUrl . '_HD.' . $possibleFileTypes[$hdFileTypeNum]))
							{
								$bbFLVUrlHD= JURI::base() .$bbRawFLVUrl . '_HD.' . $possibleFileTypes[$hdFileTypeNum];
								break;
							}
						}
					}
					//---
					
					$bbAHref=$bbFileTypeVal;
	
					if ($this->params->get('enableAutoplay') && ($bbFileType=="yt"))
					{
						$bbAHref.="&autoplay=1";
					}
					
					if ($this->params->get('lbType')=='Shadowbox')
					{					
						if ($this->params->get('enableVidRescale'))
						{
							$bbVidWidth=$this->params->get('vidWidth');
							$bbVidHeight=$this->params->get('vidHeight');
						}
						else if (($bbFileType=="swf") && !$bbVidManualDimension)
						{
							$bbVidSize=getimagesize($bbFileTypeVal);
							$bbVidWidth=$bbVidSize[0];
							$bbVidHeight=$bbVidSize[1];
						}
						
						if ($bbVidManualDimension || $this->params->get('enableVidRescale') || ($bbFileType=="swf"))
						{
							$bbRelAttrib.=';width=' . $bbVidWidth . ';height=' . $bbVidHeight;
						}
						
						if ($bbFileType=="yt")
						{
							$bbRelAttrib.=';player=swf';
						}
						else
						{
						 	//options={counterType:\'skip\',continuous:true,animSequence:\'sync\'
							//$flashvars.="{plugins=hd-1,hd.file=" . $bbFLVUrlHD . ",hd.state=false}";
							
							//$bbRelAttrib.=";options={flashVars:'plugins':'hd-1','hd.file'='" . $bbFLVUrlHD . "','hd.state'=false}";
							
							//$bbAHref.="?plugins=hd-1&hd.file=" . $bbFLVUrlHD . "&hd.state=false";
							
							$bbFLVUrl = JURI::base() . str_replace('"', '', $bbAHref);
							$bbAHref=JURI::base().'plugins/content/bottleBotLightbox/player/player.php?bbFLVLoc=' . $bbFLVUrl . '&bbFLVWidth=' . $bbVidWidth . '&bbFLVHeight=' . $bbVidHeight . '&bbFLVAutoplay=' . $this->params->get('enableAutoplay') . '&bbFLVID=SB' . $i . '&bbFLVUrlHD=' . $bbFLVUrlHD;
						}
					}
					
				}
				else													// Image
				{
					$bbAHref=$this->params->get('folder') . $gfolder . $gBigName;
				}
				
				$bbRelAttrib.= $gAddLBParams . '"';
				
				// --------------------
				
				if ($this->params->get('lbType')=='FancyBox')
				{
					// FancyBox
					
					$gFBID='fancyBox' . $i;
					$gFBAnimType=$this->params->get('fBAnimType');
					$gFBTitlePos=$this->params->get('fBTitlePos');
					$gFBTitleShow='true';
					$gFBAdditionalParams='';
					$gFBSizeParams='';
					
					if ($bbFileType!="")
					{
						$gFBSizeParams="";
						
						if ($this->params->get('enableVidRescale'))
						{
							$gFBSizeParams=',
							\'width\'		: ' . $this->params->get('vidWidth') . ',
							\'height\'		: ' . $this->params->get('vidHeight');
							
							$bbFLVWidth=$this->params->get('vidWidth');
							$bbFLVHeight=$this->params->get('vidHeight');
						}
						else if ($bbVidManualDimension)
						{
							$gFBSizeParams=',
							\'width\'		: ' . $bbVidWidth . ',
							\'height\'		: ' . $bbVidHeight;
							
							$bbFLVWidth=$bbVidWidth;
							$bbFLVHeight=$bbVidHeight;
						}
						else if ($bbFileType=="swf")
						{
							$bbVidSize=getimagesize($bbFileTypeVal);
							
							$gFBSizeParams=',
							\'width\'		: ' . $bbVidSize[0] . ',
							\'height\'		: ' . $bbVidSize[1];
						}
						
						if (($bbFileType=="yt") || ($bbFileType=="swf"))
						{
							$gFBAdditionalParams=',
							\'padding\'		: 0,
							\'autoScale\'		: false,
							\'title\'			: \'' . $gImgTitle . '\',
							\'type\'			: \'swf\',
							\'swf\'			: {
								\'wmode\'			: \'opaque\',
								\'allowfullscreen\'	: \'true\'
								}';
						}
						
						if (($bbFileType=="flv") || ($bbFileType=="mp4") || ($bbFileType=="m4v") || ($bbFileType=="mov"))
						{
							$bbFLVUrl = JURI::base() . str_replace('"', '', $bbAHref);
							
							$gFBAdditionalParams=',
							\'padding\'	: 0,
							\'autoScale\'	: false,
							\'title\'		: \'' . $gImgTitle . '\',
							\'type\'		: \'iframe\'';
							
							$bbAHref=JURI::base().'plugins/content/bottleBotLightbox/player/player.php?bbFLVLoc=' . $bbFLVUrl . '&bbFLVWidth=' . $bbFLVWidth . '&bbFLVHeight=' . $bbFLVHeight . '&bbFLVAutoplay=' . $this->params->get('enableAutoplay') . '&bbFLVID=' . $gFBID . '&bbFLVUrlHD=' . $bbFLVUrlHD;
						}
					}
					
					if (trim($gImgTitle)=="")
					{
						$gFBTitleShow='false';
					}
					
					$limage='<script type="text/javascript">
						$(document).ready(function() {
						
						$("a#' . $gFBID . '").fancybox({';
					
					if ($gFBTitlePos!='')
					{
						$limage.='
							\'titlePosition\'	: \'' . $gFBTitlePos . '\',';
					}
					
					$limage.='
							\'titleShow\'	: ' . $gFBTitleShow . ',
							\'transitionIn\'	: \'' . $gFBAnimType . '\',
							\'transitionOut\'	: \'' . $gFBAnimType . '\'' . $gFBAdditionalParams . $gFBSizeParams . '
							
						});
					});</script>';
					
					$limage.= '<a id="' . $gFBID . '" href="' . $bbAHref . '" rel="' . $bbRelAttrib . ' class="' . $this->params->get('lnkClass') . '" ' . $this->bottlebotlightbox_checkAttIfEmpty('title', $gImgTitle) .' onFocus="if(this.blur)this.blur()">' . $rawImg . '</a>';
				}
				else
				{
					// non FancyBox	
					
					$bbAAttribs='rel="' . $bbRelAttrib . ' class="' . $this->params->get('lnkClass') . '" ' . $this->bottlebotlightbox_checkAttIfEmpty('title', $gImgTitle) .' onFocus="if(this.blur)this.blur()"';
					
					$limage= '<a href="' . $bbAHref . '" ' . $bbAAttribs . '>' . $rawImg . '</a>';
				}
			}
			
			if ($bbDebug==1)
			{
				echo '<br><font color="lightblue"><b>Relpacement:</b> ' . htmlentities($limage) . '</font>';
			}
			
			$newOutput = str_replace( $myMatch[0], $limage, $newOutput );
			$i++;
		}
		
		return $newOutput;
	}
	
	protected function bottlebotlightbox_checkAttIfEmpty($gAtt, $gString)
	{
		$checkedString="";
		
		if (trim($gString)=="")
		{
				$checkedString="";
		}
		else 
		{
			$checkedString=strtolower($gAtt) . '="' .$gString. '" ';
		}
		
		return  $checkedString ;
	}
	
	protected function getImgAtt($imgAtt, $imgMatch)
	{
		$gImgAttValue='';
		$imgAttMatches = array();
		preg_match( "#" . $imgAtt . "\s*=\s*\"(.*)\"#Ui", $imgMatch, $imgAttMatches );
		if (isset($imgAttMatches[1]))
		{
		   $gImgAttValue =  trim($imgAttMatches[1]);
		}
		/*
		if ($bbDebug==1)
		{
			echo "<font color=\"red\">" . htmlentities($imgMatch) . "</font><br>";
			echo $imgAtt . ": " . $gImgAttValue . "<br><br>";
		}
		*/
		return $gImgAttValue;
	}
	
	protected function getLnkAtt($imgAtt, $imgMatch)
	{
		$gImgAttValue='';
		$imgAttMatches = array();
		preg_match( "#<a[^<^>]*?" . $imgAtt . "\s*?=\s*?\"(.*?)\"[^<^>]*?>#i", $imgMatch, $imgAttMatches );
		if (isset($imgAttMatches[1]))
		{
		   $gImgAttValue =  $imgAttMatches[1];
		}
		
		/*
		if ($bbDebug==1)
		{
			echo "<font color=\"green\">" . htmlentities($imgMatch) . "</font><br>";
			echo $imgAtt . ": " . $gImgAttValue . "<br><br>";
		}
		*/
		return $gImgAttValue;
	}
	
	protected function getValidLangPath($myLangStr, $myPath)
	{
		$validPath = str_replace("bbBBLANGbb", $myLangStr, $myPath);
		
		$myFile = @fopen($validPath, "r");
		
		if (!$myFile)
		{
			$validPath = str_replace("bbBBLANGbb", "en", $myPath);
		}
		else
		{
			fclose($myFile);
		}
		
		return $validPath;
	}
	
	protected function checkIfTrue($myBool, $myValue)
	{
		if ($myBool) return $myValue;
		else return '';
	}
}
