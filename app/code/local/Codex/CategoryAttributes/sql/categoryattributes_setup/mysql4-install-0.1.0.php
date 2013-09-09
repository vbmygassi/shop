<?php

$installer = $this;
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$installer->startSetup();

$setup->addAttribute('catalog_category', 'category_color', array(
    'group'         => 'Display Settings',
    'input'         => 'text',
    'type'          => 'varchar',
    'label'         => 'Farbe (HEX)',
    'visible'       => true,
    'required'      => false,
    'user_defined'  => true,
    'visible_on_front' => true,
    'unique'        => false,
	'filterable'    => false,
	'is_filterable' => false,
    'sort_order'      => 500
));
$installer->endSetup();
