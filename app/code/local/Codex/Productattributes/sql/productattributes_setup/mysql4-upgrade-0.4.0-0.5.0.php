<?php

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$installer->startSetup();

$setup->addAttribute('catalog_product', 'globalname', array(
    'group'                      => 'Karlie',
    'label'                      => 'Parent-Name',
    'type'                       => 'varchar',
    'input'                      => 'text',
    'frontend'                   => '',
    'source'                     => '',
    'visible'                    => true,
    'required'                   => false,
    'user_defined'               => true,
    'is_user_defined'            => true,
    'searchable'                 => true,
    'filterable'                 => true,
    'comparable'                 => true,
    'visible_on_front'           => true,
    'visible_in_advanced_search' => true,
    'unique'                     => false
));


$installer->endSetup();