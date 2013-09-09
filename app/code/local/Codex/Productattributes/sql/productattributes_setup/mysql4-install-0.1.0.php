<?php

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$installer->startSetup();

$setup->addAttribute('catalog_product', 'packaging_unit', array(
    'group'                      => 'Karlie',
    'label'                      => 'Packing unit',
    'type'                       => 'text',
    'input'                      => 'text',
    'frontend'                   => '',
    'source'                     => '',
    'visible'                    => true,
    'required'                   => false,
    'user_defined'               => true,
    'is_user_defined'            => true,
    'searchable'                 => false,
    'filterable'                 => true,
    'comparable'                 => false,
    'visible_on_front'           => true,
    'visible_in_advanced_search' => true,
    'unique'                     => false
));

$setup->addAttribute('catalog_product', 'min_qty', array(
    'group'                      => 'Karlie',
    'label'                      => 'Minimum order quantity',
    'type'                       => 'text',
    'input'                      => 'text',
    'frontend'                   => '',
    'source'                     => '',
    'visible'                    => true,
    'required'                   => false,
    'user_defined'               => true,
    'is_user_defined'            => true,
    'searchable'                 => false,
    'filterable'                 => false,
    'comparable'                 => false,
    'visible_on_front'           => false,
    'visible_in_advanced_search' => false,
    'unique'                     => false
));

$setup->addAttribute('catalog_product', 'ean', array(
    'group'                      => 'Karlie',
    'label'                      => 'EAN',
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
    'unique'                     => true
));

$setup->addAttribute('catalog_product', 'vat', array(
    'group'                      => 'Karlie',
    'label'                      => 'VAT',
    'type'                       => 'text',
    'input'                      => 'text',
    'frontend'                   => '',
    'source'                     => '',
    'visible'                    => false,
    'required'                   => false,
    'user_defined'               => true,
    'is_user_defined'            => true,
    'searchable'                 => true,
    'filterable'                 => false,
    'comparable'                 => false,
    'visible_on_front'           => true,
    'visible_in_advanced_search' => false,
    'unique'                     => false
));

$setup->addAttribute('catalog_product', 'infrastat_number', array(
    'group'                      => 'Karlie',
    'label'                      => 'Intrastat number',
    'type'                       => 'text',
    'input'                      => 'text',
    'frontend'                   => '',
    'source'                     => '',
    'visible'                    => false,
    'required'                   => false,
    'user_defined'               => true,
    'is_user_defined'            => true,
    'searchable'                 => true,
    'filterable'                 => false,
    'comparable'                 => false,
    'visible_on_front'           => true,
    'visible_in_advanced_search' => true,
    'unique'                     => true
));

$setup->addAttribute('catalog_product', 'length', array(
    'group'                      => 'Karlie',
    'label'                      => 'Länge',
    'type'                       => 'text',
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

$setup->addAttribute('catalog_product', 'width', array(
    'group'                      => 'Karlie',
    'label'                      => 'Breite',
    'type'                       => 'text',
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

$setup->addAttribute('catalog_product', 'height', array(
    'group'                      => 'Karlie',
    'label'                      => 'Höhe',
    'type'                       => 'text',
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

$setup->addAttribute('catalog_product', 'diameter', array(
    'group'                      => 'Karlie',
    'label'                      => 'Diameter',
    'type'                       => 'text',
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

$setup->addAttribute('catalog_product', 'volume', array(
    'group'                      => 'Karlie',
    'label'                      => 'Volume',
    'type'                       => 'text',
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

$setup->addAttribute('catalog_product', 'size', array(
    'group'                      => 'Karlie',
    'label'                      => 'Größe',
    'type'                       => 'text',
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

$setup->addAttribute('catalog_product', 'bargain', array(
    'group'                      => 'Karlie',
    'label'                      => 'Bargain',
    'type'                       => 'text',
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

$setup->addAttribute('catalog_product', 'new', array(
    'group'                      => 'Karlie',
    'label'                      => 'new',
    'type'                       => 'text',
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