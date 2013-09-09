<?php

$installer = $this;
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$installer->startSetup();

$setup->addAttribute('catalog_category', 'erp_id', array(
    'group' => 'Display Settings',
    'input' => 'text',
    'type' => 'varchar',
    'label' => 'ERP Id',
    'visible' => true,
    'required' => false,
    'user_defined' => true,
    'visible_on_front' => false,
    'unique' => true,
    'sort_order' => 500
));

$installer->endSetup();
