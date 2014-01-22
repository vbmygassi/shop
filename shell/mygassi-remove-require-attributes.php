<?php
/**
 * changes annoying :))) req attributes 
 */
require_once("mygassi-config.php");
require_once("mygassi-logger.php");
require_once(mageroot); 
Mage::app();
logger("Starting: mygassi-remove-req-attrs");
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$setup->updateAttribute("catalog_product", "weight", "is_required", 0);
$setup->updateAttribute("catalog_product", "basisartikelnr", "is_required", 0);
$setup->endSetup();	
logger("Done: mygassi-install");
exit(1);
