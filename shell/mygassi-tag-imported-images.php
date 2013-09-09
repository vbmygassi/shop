<?php
require_once("mygassi-config.php");
require_once("mygassi-logger.php");
require_once(mageroot);

Mage::app();

$coll = Mage::getModel("catalog/product")->getCollection();

foreach($coll as $product){
	$prod = $product->load($product->getId());
	foreach($prod->getMediaGalleryImages() as $image){
		$sku  = $prod->getSku();
		$target = "";
		$dest = "";
		$filename = $image->getFile();
		$temp = explode(".", $filename);
		$filename = $temp[0];
		$suff = $temp[1];
		$filename = explode("/", $filename);
		$filename = array_pop($filename);
		$filename = explode("_", $filename);
		$filename = $filename[0];
		$filename = $filename . "." . $suff;
		$target = Mage::getBaseDir("media") . DS . "import401" . DS . $filename;
		$dest = Mage::getBaseDir("media") . DS . "import401" . DS . $sku . "." . $filename;
		// print "target: " . $target . "\n";
		// print "dest:   " . $dest . "\n";		
		if(is_file($target)){
			print "target: " . $target . "\n";
			print "dest:   " . $dest . "\n";		
			rename($target, $dest);
		}
		else {
			print "!!!!!!!!!!!!!!target: " . $target . "\n";
		}
	}
}	
