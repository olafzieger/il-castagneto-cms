<?PHP
defined('JPATH_BASE') or die;

#JHtml::_('behavior.framework', true);
#jimport('joomla.html.html.bootstrap');
#JHTML::_('behavior.bootstrap'); 
JHtml::_('jquery.framework');

#JHTml::_('behavior.jquery');

$path="plugins/system/imagesizer/lbscripts/lightbox2/";

$lang =& JFactory::getLanguage();
$l=substr($lang->getTag(),0,2);
$document   =& JFactory::getDocument();

#	$document->addScript('/media/jui/js/jquery.ui.core.min.js');

if(file_exists(JPATH_SITE."/".$path.$l.'_lightbox2.css')){
	$document->addStyleSheet($path.$l.'_lightbox2.css','text/css',"all");	
}else{
	$document->addStyleSheet($path.'lightbox2.css','text/css',"all");
}


if(file_exists(JPATH_SITE.DS.$path.$l.'_lightbox2.js')){

	$document->addScript($path.$l.'_lightbox2.js');
}else{
	$document->addScript($path.'lightbox2.js');	
}
/*
$document->addScriptDeclaration('
$(document).ready(function() {
});
');
*/


unset($path);


function ImageSizer_addon_lightbox2(&$ar,&$imagesizer){

	$output=plgSystemimagesizer::make_img_output($ar);

	$x=explode("/",$ar["href"]);
	$c=count($x)-1;
	$x[$c]=rawurlencode($x[$c]);
	$x=implode("/",$x);

	if(isset($ar["title"])){
		$title=' title="'.$ar["title"].'"';
	}else{
		if(isset($ar["alt"])){
			$title=' title="'.$ar["alt"].'"';
		}else{
			$title="";
		} 
	} 


	$id=0;
	
	if(isset($imagesizer->article->id)){
		$id=$imagesizer->article->id;
	}
	 		
	
	$output='<a class="'.trim($imagesizer->params->get("linkclass","linkthumb")).'" target="_blank"'.$title.' rel="lightbox[id_'.$id.']" href="'.$x.'"><img '.$output.' /></a>';	

	return $output;

}

