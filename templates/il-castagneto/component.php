<?php
/**
 * @version SVN: $Id: header.php 18 2010-11-08 01:10:19Z elkuku $
 * @package    il-castagneto
 * @subpackage Base
 * @author     K.-Michael Siebenlist {@link http://www.kms-net.de}
 * @author     Created on 27-May-2011
 * @license    GNU/GPL
 */

//-- No direct access
defined('_JEXEC') || die('=;)');
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>"
lang="<?php echo $this->language; ?>"
dir="<?php echo $this->direction; ?>">
<head>
   <jdoc:include type="head" />
   <link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php $this->template; ?>/css/template.css" type="text/css" />
</head>
<body class="contentpane">
   <jdoc:include type="message" />
   <jdoc:include type="component" />
</body>
</html>