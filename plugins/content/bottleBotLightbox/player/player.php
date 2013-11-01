<?php
/**
 * BottleBot Lightbox 1.3 for Joomla! 1.5
 * @license http://www.gnu.org/licenses/gpl.html GNU/GPL.
 * @by bottlebot
 * @Copyright (C) 2011 BottleBot
 */

$bbFLVLoc=$_GET['bbFLVLoc'];
$bbFLVTitle=$_GET['bbFLVTitle'];
$bbFLVWidth=$_GET['bbFLVWidth'];
$bbFLVHeight=$_GET['bbFLVHeight'];
$bbFLVID=$_GET['bbFLVID'];
$bbFLVAutoplay=$_GET['bbFLVAutoplay'];
$bbFLVUrlHD=$_GET['bbFLVUrlHD'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de-de" lang="de-de" dir="ltr">

<head>
<title><?php echo $bbFLVTitle ?></title>
<style type="text/css">
body {
	margin: 0 0 0 0;
	padding: 0px solid;
	overflow: hidden;
}
</style>
</head>

<body>
<embed
<?php 
	$flashvars="file=" . $bbFLVLoc . "&autostart=" . $bbFLVAutoplay;
	 
	if (trim($bbFLVUrlHD)!="")
	{
	 	$flashvars.="&plugins=hd-1&hd.file=" . $bbFLVUrlHD . "&hd.state=false";
	}
	
	$flashvars.= "&backcolor=000000&frontcolor=CCCCCC&lightcolor=557722";
	echo "flashvars=\"" . $flashvars . "\""; 
?>
 allowfullscreen="true"
 allowscripaccess="always"
 id="<?php echo $bbFLVID ?>"
 name="<?php echo $bbFLVID ?>"
 src="../sb/player.swf"
 wmode="opaque"
 
 width="<?php echo $bbFLVWidth ?>" 
 height="<?php echo $bbFLVHeight ?>"
/>
</body>
</html>