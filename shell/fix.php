<?php

require_once("mygassi-config.php");
require_once(mageroot);
Mage::app();

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$setup->startSetup();
$setup->deleteTableRow('eav/entity_attribute', 'attribute_id', $setup->getAttributeId('catalog_product', 'linientext'), 'attribute_set_id', $setup->getAttributeSetId('catalog_product', 'Default'));
$setup->deleteTableRow('eav/entity_attribute', 'attribute_id', $setup->getAttributeId('catalog_product', 'gruppentext'), 'attribute_set_id', $setup->getAttributeSetId('catalog_product', 'Default'));
$setup->deleteTableRow('eav/entity_attribute', 'attribute_id', $setup->getAttributeId('catalog_product', 'einzeltext'), 'attribute_set_id', $setup->getAttributeSetId('catalog_product', 'Default'));
$setup->deleteTableRow('eav/entity_attribute', 'attribute_id', $setup->getAttributeId('catalog_product', 'add_desc_1'), 'attribute_set_id', $setup->getAttributeSetId('catalog_product', 'Default'));
$setup->deleteTableRow('eav/entity_attribute', 'attribute_id', $setup->getAttributeId('catalog_product', 'add_desc_2'), 'attribute_set_id', $setup->getAttributeSetId('catalog_product', 'Default'));
$setup->deleteTableRow('eav/entity_attribute', 'attribute_id', $setup->getAttributeId('catalog_product', 'add_desc_3'), 'attribute_set_id', $setup->getAttributeSetId('catalog_product', 'Default'));
$setup->endSetup();


exit();




