<?php
/**
 * @version SVN: $Id: header.php 18 2010-11-08 01:10:19Z elkuku $
 * @package    kms-blueprint
 * @subpackage Base
 * @author     K.-Michael Siebenlist {@link http://www.kms-net.de}
 * @author     Created on 27-May-2011
 * @license    GNU/GPL
 */

// Verhindern, dass diese php-Datei direkt aufgerufen wird
defined('_JEXEC') or die;

$app = JFactory::getApplication();
 //Parameter des Templates laden
$templateparams   = $app->getTemplate(true)->params;
?>
<?php echo '<?xml version="1.0" encoding="utf-8"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>"
lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>" >
    <head>
 
        <jdoc:include type="head" />
 
        <link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/template.css" type="text/css" />
    </head>
    <body>
        <div id="container">
 
            <div id="header">
                <h1 class="sitename">
               <?php
               echo $app->getCfg('sitename');           //Seitenname aus der Konfiguration ausgeben
//                echo $templateparams->get('sitetitle')."<br>";  //Seitenname Template-Parameter
//                echo $templateparams->get('sitedescription');   //Beschreibung Template-Parameter
               ?>
                </h1>
                <div class="h2">
                	<div class="line"></div>
                	Urlaub in der Gemeinschaft
                </div>
                <jdoc:include type="modules" name="position-1" />
            </div>
 
            <div id="main">
            <?php echo $templateparams->get('hintergrundbild'); ?>
<!--              <div id="links"> -->
 
                    <jdoc:include type="modules" name="position-7" style="xhtml" />

<!--                 </div> -->
                <div id="content">
 
                    <?php
                        // Das Error Div wird nur eingebunden, wenn eine Nachricht existiert
                        if ($this->getBuffer('message')) :
                    ?> 
                            <div class="error"> 
                                <jdoc:include type="message" />
                            </div>
                    <?php endif; ?>

                <jdoc:include type="component" />
                </div>
            
            <?php
                  // Dieses Div wird nur eingebunden, wenn Module mit der Position position-2 vorhanden sind
                 if ($this->countModules( 'position-2' )) : ?>               
<!--                    <div id="rechts"> -->
                 
                  <?php        
                  // Diese Module werden nur nach einem Login angezeigt
                  $user = &JFactory::getUser();
                  $userid = $user->get('id');
                     If ($userid > 0) :   ?>
                        <jdoc:include type="modules" name="position-2" style="xhtml" />
                      <!-- ansonsten werden dieses Module angezeigt -->
                       <?php else : ?>
                        <jdoc:include type="modules" name="position-17" style="xhtml" />
                     <?php endif ?>                  
<!--                    </div> -->
                <?php endif; ?>
 
            </div>
            <div id="fuss">
            	<jdoc:include type="modules" name="position-14" style="xhtml" />
            </div>
        </div>
    </body>
</html>