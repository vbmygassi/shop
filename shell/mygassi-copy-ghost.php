<?php
/**
 * Creates a ghost copy of the default shop-view
 * The ghost copy will not get edited at importing new products
 */

require_once("mygassi-config.php");
require_once("mygassi-logger.php");
require_once(mageroot);

Mage::app();

$coll = Mage::getModel("catalog/product")->getCollection();

$ghostStoreId = getStoreId("ghost");
print "ghostStoreId: " . $ghostStoreId;

foreach($coll as $product){
	$prod = $product->load($product->getId());
	$prod->setStoreId($ghostStoreId);
	$prod->save();
}

function getStoreId($code="default")
{
	$allStores = Mage::app()->getStores();
	$res = 0;
	foreach ($allStores as $eachStoreId => $val) {
		$storeCode = Mage::app()->getStore($eachStoreId)->getCode();
		$storeId = Mage::app()->getStore($eachStoreId)->getId();
		if($code === $storeCode){ $res = $storeId; }
	}
	return $res;
}

exit(1);
