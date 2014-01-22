<?php 
/**
 * setup of tracking flags
 */
require_once("mygassi-config.php");
require_once("mygassi-logger.php");
require_once(mageroot); 
Mage::app();

logger("Starting: mygassi-install");

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');

$setup->updateAttribute("catalog_product", "weight", "is_required", 0);
$setup->updateAttribute("catalog_product", "basisartikelnr", "is_required", 0);

/*
$res = $setup->addAttribute('catalog_product', 'group_id', array(
	'group'                      => 'MyGassi',
	'label'                      => 'Produktgruppen ID',
	'type'                       => 'int',
	'input'                      => 'text',
	'frontend'                   => '',
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

$res = $setup->addAttribute('catalog_product', 'category_ids', array(
	'group'                      => 'MyGassi',
	'label'                      => 'Kategorien ID',
	'type'                       => 'int',
	'input'                      => 'text',
	'frontend'                   => '',
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
*/

exit(1);

/*
$res = $setup->addAttribute('catalog_category', 'cpl', array(
	'group'			=> 'Display Settings',
	'label'			=> 'Category-Product-Link',
	'type'			=> 'varchar',
	'input'			=> 'text',
    	'visible'		=> true,
	'required' 		=> false,
    	'user_defined' 		=> true,










exit(1);

/*
$res = $setup->addAttribute('catalog_category', 'cpl', array(
	'group'			=> 'Display Settings',
	'label'			=> 'Category-Product-Link',
	'type'			=> 'varchar',
	'input'			=> 'text',
    	'visible'		=> true,
	'required' 		=> false,
    	'user_defined' 		=> true,
	'visible_on_front' 	=> false,




/*
function addOrderState($args)
{
	$installer = new Mage_Core_Model_Resource_Setup();
	$installer->startSetup();
	logger($installer->getTable('sales/order_status'));
	foreach($args as $arg)
	{
		$sql = "INSERT INTO  `{$installer->getTable('sales/order_status')}` (
			`status` ,
			`label`
		) VALUES (
			'".$arg["status"]."',  '".$arg["label"]."'
		);
		INSERT INTO  `{$installer->getTable('sales/order_status_state')}` (
			`status` ,
			`state` ,
			`is_default`
		) VALUES (
			'".$arg["status"]."',  '".$arg["state"]."',  '0'
		);";
		logger($sql);
		try{ 
			$installer->run($sql); 
		} 
		catch(Extension $e){ 
			logger($e->getMessage()); 
		}
	}
	$installer->endSetup();
}
*/

/*
addOrderState(
	$args = array(
		array(
			status => "retoure",
			label  => "Lieferung wird zurückgesandt",
			state  => "processing"
		),
		array(
			status => "wait-for-payone",
			label  => "Payone arbeitet",
			state  => "processing"
		),
		array(
			status => "sent",
			label  => "Versandt von Karlie",
			state  => "processing"
		),
		array(
			status => "referred_to_karlie",
			label  => "An Karlie gesandt",
			state  => "processing"
		),
		array(
			status => "appointed",
			label  => "Auftrag angenommen",
			state  => "processing"
		),
		array(
			status => "captured",
			label  => "Eingezogen",
			state  => "processing"
		),
		array(
			status => "payed",
			label  => "Bezahlt",
			state  => "processing"
		),
		array(
			status => "underpayed",
			label  => "Nicht vollständig gezahlt",
			state  => "processing"
		),
		array(
			status => "canceled",
			label  => "Zahlung storniert",
			state  => "processing"
		),
		array(
			status => "refund",
			label  => "Rückerstattet",
			state  => "processing"
		),
		array(
			status => "debt",
			label  => "Eingefordert",
			state  => "processing"
		),
		array(
			status => "reminded",
			label  => "Gemahn",
			state  => "processing"
		),
		array(
			status => "vauthorized",
			label  => "Gebucht",
			state  => "processing"
		),
		array(
			status => "vsettled",
			label  => "Abgerechnet",
			state  => "processing"
		),
		array(
			status => "transferd",
			label  => "Umgebucht",
			state  => "processing"
		),
		array(
			status => "invoiced",
			label  => "Rechnung erstellt",
			state  => "processing"
		) 
	)
);
*/

/*
$setup->addAttribute('catalog_product', 'add_desc_1', array(
	'group'				=> 'MyGassi',
	'backend_type'			=> 'text',
	'type'				=> 'text',
	'input'				=> 'textarea',
	'frontend_input'		=> 'textarea',
	'visible'			=> true,
	'label'				=> 'Zusätzliche Beschreibung 1',
	// 'global'			=> Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
	'required'			=> false,
	'default'			=> '',
	'searchable'			=> false,
	'filterable'			=> false,
	'comparable'			=> false,
	'visible_on_front'		=> true,
	'used_in_product_listing'	=> false,
	'unique'			=> false,
));

$setup->addAttribute('catalog_product', 'add_desc_2', array(
	'group'				=> 'MyGassi',
	'backend_type'			=> 'text',
	'type'				=> 'text',
	'input'				=> 'textarea',
	'frontend_input'		=> 'textarea',
	'visible'			=> true,
	'label'				=> 'Zusätzliche Beschreibung 2',
	// 'global'			=> Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
	'required'			=> false,
	'default'			=> '',
	'searchable'			=> false,
	'filterable'			=> false,
	'comparable'			=> false,
	'visible_on_front'		=> true,
	'used_in_product_listing'	=> false,
	'unique'			=> false,
));

$setup->addAttribute('catalog_product', 'add_desc_3', array(
	'group'				=> 'MyGassi',
	'backend_type'			=> 'text',
	'type'				=> 'text',
	'input'				=> 'textarea',
	'frontend_input'		=> 'textarea',
	'visible'			=> true,
	'label'				=> 'Zusätzliche Beschreibung 3',
	// 'global'			=> Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
	'required'			=> false,
	'default'			=> '',
	'searchable'			=> false,
	'filterable'			=> false,
	'comparable'			=> false,
	'visible_on_front'		=> true,
	'used_in_product_listing'	=> false,
	'unique'			=> false,
));

$setup->addAttribute('catalog_category', 'add_desc_1', array(
	'group'				=> 'Default',
	'backend_type'			=> 'text',
	'type'				=> 'text',
	'input'				=> 'textarea',
	'frontend_input'		=> 'textarea',
	'visible'			=> true,
	'label'				=> 'Zusätzliche Beschreibung 1',
	// 'global'			=> Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
	'required'			=> false,
	'default'			=> '',
	'searchable'			=> false,
	'filterable'			=> false,
	'comparable'			=> false,
	'visible_on_front'		=> true,
	'used_in_product_listing'	=> false,
	'unique'			=> false,
));

$setup->addAttribute('catalog_category', 'add_desc_2', array(
	'group'				=> 'Default',
	'backend_type'			=> 'text',
	'type'				=> 'text',
	'input'				=> 'textarea',
	'frontend_input'		=> 'textarea',
	'visible'			=> true,
	'label'				=> 'Zusätzliche Beschreibung 2',
	// 'global'			=> Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
	'required'			=> false,
	'default'			=> '',
	'searchable'			=> false,
	'filterable'			=> false,
	'comparable'			=> false,
	'visible_on_front'		=> true,
	'used_in_product_listing'	=> false,
	'unique'			=> false,
));

$setup->addAttribute('catalog_category', 'add_desc_3', array(
	'group'				=> 'Default',
	'backend_type'			=> 'text',
	'type'				=> 'text',
	'input'				=> 'textarea',
	'frontend_input'		=> 'textarea',
	'visible'			=> true,
	'label'				=> 'Zusätzliche Beschreibung 3',
	// 'global'			=> Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
	'required'			=> false,
	'default'			=> '',
	'searchable'			=> false,
	'filterable'			=> false,
	'comparable'			=> false,
	'visible_on_front'		=> true,
	'used_in_product_listing'	=> false,
	'unique'			=> false,
));
*/

/*
$res = $setup->addAttribute('catalog_product', 'linientext', array(
	'group'                      => 'MyGassi',
	'label'                      => 'Linientext',
	'backend_type'               => 'text',
	'type'				=> 'textarea',
	'frontend_input'		=> 'textarea',
	'visible'                    => true,
	'required'                   => false,
	'user_defined'               => true,
	'is_user_defined'            => true,
	'searchable'                 => true,
	'filterable'                 => true,
	'comparable'                 => false,
	'visible_on_front'           => false,
	'visible_in_advanced_search' => false,
	'unique'                     => false 
));

$res = $setup->addAttribute('catalog_product', 'gruppentext', array(
	'group'                      => 'MyGassi',
	'label'                      => 'Gruppentext',
	'backend_type'               => 'text',
	'type'				=> 'textarea',
	'frontend_input'		=> 'textarea',
	'visible'                    => true,
	'required'                   => false,
	'user_defined'               => true,
	'is_user_defined'            => true,
	'searchable'                 => true,
	'filterable'                 => true,
	'comparable'                 => false,
	'visible_on_front'           => false,
	'visible_in_advanced_search' => false,
	'unique'                     => false 
));

$res = $setup->addAttribute('catalog_product', 'einzeltext', array(
	'group'                      => 'MyGassi',
	'label'                      => 'Einzeltext',
	'backend_type'               => 'text',
	'type'				=> 'textarea',
	'frontend_input'		=> 'textarea',
	'visible'                    => true,
	'required'                   => false,
	'user_defined'               => true,
	'is_user_defined'            => true,
	'searchable'                 => true,
	'filterable'                 => true,
	'comparable'                 => false,
	'visible_on_front'           => false,
	'visible_in_advanced_search' => false,
	'unique'                     => false 
));
*/

/*
$setup->addAttribute('customer', 'invoice_coupon', array(
	'input'         => 'text',
	'type'          => 'text',
	'label'         => 'Coupon',
	'visible'       => 1,
	'required'      => 0,
	'user_defined'  => 0
));

$setup->addAttribute('customer', 'newsletter_coupon', array(
	'input'         => 'text',
	'type'          => 'text',
	'label'         => 'Coupon',
	'visible'       => 1,
	'required'      => 0,
	'user_defined'  => 0
));

$res = $setup->addAttribute('order', 'karlie_order_id', array(
	'label'                      => 'Karlie Order ID',
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
	'comparable'                 => false,
	'visible_on_front'           => true,
	'visible_in_advanced_search' => false,
	'unique'                     => true 
));
*/

/**
 * adds parcel-id attribute to sale
 */
/*
$res = $setup->addAttribute('order', 'parcel_id', 
	array(
	'label'                      => 'Parcel ID',
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
	'comparable'                 => false,
	'visible_on_front'           => true,
	'visible_in_advanced_search' => false,
	'unique'                     => false 
	)
);

$res = $setup->addAttribute('quote', 'parcel_id', 
	array(
	'label'                      => 'Parcel ID',
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
	'comparable'                 => false,
	'visible_on_front'           => true,
	'visible_in_advanced_search' => false,
	'unique'                     => false 
	)
);
*/

/*
$res = $setup->addAttribute('catalog_product', 'basisartikelnr', array(
	'group'                      => 'MyGassi',
	'label'                      => 'Karlie Basis Artikel Nr.',
	'type'                       => 'text',
	'input'                      => 'text',
	'frontend'                   => '',
	'source'                     => '',
	'visible'                    => true,
	'required'                   => true,
	'user_defined'               => true,
	'is_user_defined'            => true,
	'searchable'                 => false,
	'filterable'                 => true,
	'comparable'                 => false,
	'visible_on_front'           => false,
	'visible_in_advanced_search' => false,
	'unique'                     => false 
));

$res = $setup->addAttribute('catalog_product', 'is_top_product', array(
	'group'                      => 'MyGassi',
	'label'                      => 'Top Produkt',
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

$res = $setup->addAttribute('catalog_product', 'is_stream_product', array(
	'group'                      => 'MyGassi',
	'label'                      => 'Produkt im Stream',
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
*/

/*
$res = $setup->addAttribute('catalog_category', 'cpl', array(
	'group'			=> 'Display Settings',
	'label'			=> 'Category-Product-Link',
	'type'			=> 'varchar',
	'input'			=> 'text',
    	'visible'		=> true,
	'required' 		=> false,
    	'user_defined' 		=> true,
	'visible_on_front' 	=> false,
	'unique' 		=> true,
	'sort_order' 		=> 500
));
*/

/*
try{
	Mage::getModel('core/store')->setCode("en")
		->setWebsiteId(1)
		->setGroupId(1)
		->setName('English')
		->setIsActive(1)
		->setSortOrder(1)
		->save();
} 
catch(Exception $e){
	logger("Some exception while adding store view: " . $e->getMessage());
}
*/

/*
$res = $setup->addAttribute('catalog_product', 'old_price', array(
	'group'                      => 'MyGassi',
	'label'                      => 'Streichpreis',
	'type'                       => 'text',
	'input'                      => 'text',
	'frontend'                   => '',
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
*/

$setup->updateAttribute('catalog_product','short_description','is_required', 0);
$setup->updateAttribute('catalog_product','description','is_required', 0);

$setup->endSetup();	
logger("Done: mygassi-install");
exit(1);
