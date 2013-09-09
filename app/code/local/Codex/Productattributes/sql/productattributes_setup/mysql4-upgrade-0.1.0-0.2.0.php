<?php

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$installer->startSetup();

$setup->addAttribute('catalog_product', 'artikelbezeichnung1', array(
    'group'                      => 'Karlie',
    'label'                      => 'Artikelbezeichnung1',
    'type'                       => 'text',
    'input'                      => 'text',
    'frontend'                   => '',
    'source'                     => '',
    'visible'                    => true,
    'required'                   => false,
    'user_defined'               => true,
    'is_user_defined'            => true,
    'searchable'                 => true,
    'filterable'                 => false,
    'comparable'                 => false,
    'visible_on_front'           => true,
    'visible_in_advanced_search' => true,
    'unique'                     => false
));

$setup->addAttribute('catalog_product', 'artikelbezeichnung2', array(
    'group'                      => 'Karlie',
    'label'                      => 'Artikelbezeichnung2',
    'type'                       => 'text',
    'input'                      => 'text',
    'frontend'                   => '',
    'source'                     => '',
    'visible'                    => true,
    'required'                   => false,
    'user_defined'               => true,
    'is_user_defined'            => true,
    'searchable'                 => true,
    'filterable'                 => false,
    'comparable'                 => false,
    'visible_on_front'           => true,
    'visible_in_advanced_search' => true,
    'unique'                     => false
));

$setup->addAttribute('catalog_product', 'artikelbezeichnung3', array(
    'group'                      => 'Karlie',
    'label'                      => 'Artikelbezeichnung3',
    'type'                       => 'text',
    'input'                      => 'text',
    'frontend'                   => '',
    'source'                     => '',
    'visible'                    => true,
    'required'                   => false,
    'user_defined'               => true,
    'is_user_defined'            => true,
    'searchable'                 => true,
    'filterable'                 => false,
    'comparable'                 => false,
    'visible_on_front'           => true,
    'visible_in_advanced_search' => true,
    'unique'                     => false
));


$installer->endSetup();