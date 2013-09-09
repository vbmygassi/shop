<?php
require_once("mygassi-config.php");
require_once("mygassi-logger.php");
require_once(mageroot);
Mage::app();
logger("Starting: mygassi-manipulate-product-attribute");

$coll = Mage::getModel("catalog/product")->getCollection()->addFieldToFilter("sku", "513287");
foreach($coll as $prod){ 
	$prod = $prod->load($prod->getId());
	print $prod->getName();
	print $prod->getArtikelbezeichnung1();
	$prod->setArtikelbezeichnung1("Oki");
	$prod->save();	
}

/*
$coll = Mage::getModel("catalog/product")->getCollection();
foreach($coll as $product){
	$product->load($product->getId())
		->setIsTopProduct(1)
		->setIsStreamProduct(1)
		->save();
	logger("Manipulatorizing: " . $product->getId() . " isStreamProduct and isTopProduct"); 
}
logger("Done: mygassi-manipulate-product-attribute");
*/
exit(1);
