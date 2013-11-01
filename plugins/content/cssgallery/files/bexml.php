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

jimport('joomla.form.formfield');//import the necessary class definition for formfield
class JFormFieldbexml extends JFormField {
	
	protected $type = 'bexml';

	var	$_name = 'CSS Gallery';
	var $_version = '1.3.5';

	protected function getInput(){
		$view =$this->element['view'];

		switch ($view){

		case 'intro':
            $html="<div style='background-color:#c3d2e5;margin:0;padding:2px;display:block;clear:both;'>";
            $html.="<b>".$this->_name." Version: ".$this->_version."</b><br />";
            $html.="for support and updates visit:&nbsp;";
            $html.="<a href='http://www.bretteleben.de' target='_blank'>www.bretteleben.de</a>";
            $html.="</div>";
		break;

		case 'gallery':
            $html="<div style='background-color:#c3d2e5;margin:0;padding:2px;display:block;clear:both;'>";
            $html.="<b>Gallery</b> - Settings regarding the gallery in general (see <a href='http://www.bretteleben.de/lang-en/joomla/css-gallery/installation-and-usage-plugin.html' target='_blank'>Howto Plugin</a>).";
            $html.="</div>";
		break;

		case 'thumbs':
            $html="<div style='background-color:#c3d2e5;margin:0;padding:2px;display:block;clear:both;'>";
            $html.="<b>Thumbnails</b> - Settings regarding the thumbnails like size, quality, position (see <a href='http://www.bretteleben.de/lang-en/joomla/css-gallery/installation-and-usage-plugin.html' target='_blank'>Howto Plugin</a>).";
            $html.="</div>";
		break;

		case 'gd':
            $html="<div style='background-color:#c3d2e5;margin:0;padding:2px;display:block;clear:both;'>";
            $html.="<b>GD library</b> - Information about the PHP GD library on your server. <br />The plugin uses this PHP extension to create thumbnails.";
						if(function_exists("gd_info")){
            	$html.="<br />GD is supported by your server!<br /><br />";
							$gd = gd_info();
							$be_gdarray=array(
										"gd" => "<span style='color:red'>unknown</span>",
										"jpg" => "<span style='color:red'>not enabled</span>",
										"png" => "<span style='color:red'>not enabled</span>",
										"gifr" => "<span style='color:red'>not enabled</span>",
										"gifw" => "<span style='color:red'>not enabled</span>");
							foreach ($gd as $k => $v) {
								if(stristr($k,"gd")!=FALSE){$be_gdarray["gd"]=$v;}
								if((stristr($k,"jpg")!=FALSE||stristr($k,"jpeg")!=FALSE)&&$v==1&&function_exists("imagecreatefromjpeg")){$be_gdarray["jpg"]="enabled";}
								if(stristr($k,"png")!=FALSE&&$v==1&&function_exists("imagecreatefrompng")){$be_gdarray["png"]="enabled";}
								if(stristr($k,"gif read")!=FALSE&&$v==1){$be_gdarray["gifr"]="enabled";}
								if(stristr($k,"gif create")!=FALSE&&$v==1&&function_exists("imagecreatefromgif")){$be_gdarray["gifw"]="enabled";}
							}
            	$html.="GD Version: ".$be_gdarray["gd"]."<br />";
            	$html.="JPG Support: ".$be_gdarray["jpg"]."<br />";
            	$html.="PNG Support: ".$be_gdarray["png"]."<br />";
            	$html.="GIF read Support: ".$be_gdarray["gifr"]."<br />";
            	$html.="GIF create Support: ".$be_gdarray["gifw"]."<br />";
						}else{
            	$html.="<br /><span style='color:red'>GD is not supported by your server!</span><br />";
						}
            $html.="<br />Find further information at <a href='http://www.bretteleben.de/lang-en/joomla/css-gallery/faq-a-troubleshooting.html#faq01' target='_blank'>FAQ&Troubleshooting</a>";
            $html.="</div>";
		break;

		default:
            $html="<div style='background-color:#c3d2e5;margin:0;padding:2px;display:block;clear:both;'>";
            $html.="<b>Additional Features</b><br />Enable/Disable Links and Captions (see <a href='http://www.bretteleben.de/lang-en/joomla/css-gallery/-anleitung-plugin-code.html' target='_blank'>Howto Plugin Code</a>).";
            $html.="</div>";
		break;

		}
		return $html;
	}
}