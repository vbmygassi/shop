<?php
require_once("mygassi-config.php");
require_once("mygassi-logger.php");
require_once(mageroot);
Mage::app();
logger("Starting: check-missing-images");

$coll = Mage::getModel("catalog/product")->getCollection();
$m = 0;
$n = 0;
$t = 0;
$buff = "";

foreach($coll as $prod){
	$prod = Mage::getModel("catalog/product")->setStoreId(getStoreId("default"))->load($prod->getId());
	$missing = true;
	foreach($prod->getMediaGalleryImages() as $image){
		// print $t . " : " . $n . " : SKU: " . $prod->getSku() . " image->getFile(): " . $image->getFile() . "\n";
		$missing = false;
		$n++;
	}
	if($missing){
		// print $t . " : " . $m . " : SKU: " . $prod->getSku() . " '" .$prod->getName() . "' hat kein Produktbild.\n";
		$buff .= "SKU: " . $prod->getSku() . " '" .$prod->getName() . "' hat kein Produktbild.\n";
		$m++;
	}
	$t++;
}

file_put_contents(MissingAssetLogPath, $buff);

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
	
logger("Done: check-missing-images");
exit(1);
