<?php
require_once("mygassi-config.php");
require_once("mygassi-logger.php");
require_once(mageroot);
Mage::app();
logger("Starting: mygassi-manipulate-title");

$coll = Mage::getModel("catalog/product")->getCollection();
foreach($coll as $product){
	$product->load($product->getId());
	$temp = $product->getArtikelbezeichnung2();
	if(count($temp) < 1){
		continue;
	}
	if(false === strpos($temp, "<i>")){
		continue;
	}
	$temp = str_replace("<i>", "", $temp);
	$temp = str_replace("</i>", "", $temp);
	print $temp . "\n";
	$product->setArtikelbezeichnung2($temp);
	$product->save();	
}
logger("Done: mygassi-manipulate-title");

exit(1);
