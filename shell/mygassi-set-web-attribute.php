<?php
require_once("mygassi-config.php");
require_once("mygassi-logger.php");
require_once(mageroot); 
Mage::app();

logger("Starting: mygassi-install");

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$res = $setup->addAttribute('catalog_product', 'appears_on_the_internez', array(
	'group'                      => 'MyGassi',
	'label'                      => 'Im Onlineshop sichtbar',
	'type'                       => 'int',
	'input'                      => 'select',
	'frontend'                   => '',
	'source'                     => 'eav/entity_attribute_source_boolean',
	'visible'                    => true,
	'required'                   => false,
	'user_defined'               => true,
	'is_user_defined'            => true,
	'searchable'                 => true,
	'filterable'                 => true,
	'comparable'                 => false,
	'visible_on_front'           => false,
	'visible_in_advanced_search' => false,
	'default'			=> '0',
	'unique'                     => false 
));

$setup->endSetup();	
logger("Done: mygassi-install");
exit(1);
