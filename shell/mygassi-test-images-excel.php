<?php
/****
 * Excel, das Gedärm, das aus dem Arschloch hängt.
 **/

require_once("mygassi-config.php");
require_once("mygassi-logger.php");
require_once(mageroot);

Mage::app();
logger("Starting: mygassi-test-images");

$buff = "";

$coll = Mage::getModel("catalog/product")->getCollection();
$index = 0;
foreach($coll as $prod){
	$prod = $prod->load($prod->getId());
	$sku = $prod->getSku();
	$title = $prod->getName();
	$description = $prod->getDescription();
	$groupDetail = $prod->getArtikelbezeichnung1();
	$detail = $prod->getArtikelbezeichnung2();
	$description = $prod->getDescription();
	// $price = Mage::helper("core")->currency($prod->getPrice());
	$price = $prod->getPrice(); 
	
	$buff .= "'" . $sku . "'";
	$buff .= ",";
	$buff .= "'" . $index . "'";
	$buff .= ",";
	$buff .= "'" . $title . "'";
	$buff .= ",";
	$buff .= "'" . $price . "'";
	$buff .= ",";
	$buff .= "'" . $description . "'";
	$buff .= ","; 
	$buff .= "'" . $detail . "'";
	$buff .= ",";
	$buff .= "'". $groupDetail . "'";
	
	foreach($prod->getMediaGalleryImages() as $image){
		$path = (string)Mage::helper("catalog/image")->init($prod, "thumbnail", $image->getFile())->keepFrame(false)->resize(640);
		$filename = $image->getFile();
		$filename = $image->getFile();
		$temp = explode(".", $filename);
		$filename = $temp[0];
		$suff = $temp[1];
		$filename = explode("/", $filename);
		$filename = array_pop($filename);
		$filename = explode("_", $filename);
		$filename = $filename[0];
		$filename = $filename . "." . $suff;
		$imgfile  = trim($sku) . "." . trim($filename); 
		$buff .= "'" . $imgfile . "'";
		$buff .= ",";
		$buff .= "'" . $filename . "'";
		$buff .= ",";
		$buff .= "'" . $path . "'";
		$index++;
	}
	$buff .= "\n";
}

file_put_contents(ImageTestExcelPath, $buff);

exit(1);
