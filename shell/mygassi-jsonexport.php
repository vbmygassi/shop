<?php
require_once("mygassi-config.php");
require_once("mygassi-logger.php");
require_once(mageroot);
Mage::app();

function writeProductCatalogAsJSON()
{
	$json = Zend_Json::encode(getProducts());
	$dest = JSONExportPath;
	logger("writing: " . $dest);
	logger("json:" . $json);
	$fp = fopen($dest, "w");
	fwrite($fp, $json);
	fclose($fp);
}

function getCategories()
{
	$res = array();
	$categories = Mage::getModel("catalog/category")->getCollection();
	foreach($categories as $cat){
		$cat = Mage::getModel("catalog/category")->load($cat->getId());
		$res[]= array(
			'id'=>$cat->getId(),
			'title'=>rawurlencode($cat->getName())
		);
	}
	return $res;
}

function getProducts()
{
	$mprds = array();
	$products = Mage::getModel("catalog/product")->getCollection();
	foreach($products as $product){
		$prod = Mage::getModel("catalog/product")->load($product->getId());
		$mprds[]= array(
			'category_ids'=>($prod->getCategoryIds()),
			'id'=>($prod->getId()),
			'title'=>($prod->getName()),
			'old_price'=>(getPhoneFormattedPrice($prod->getOldPrice())),
			'price'=>(getPhoneFormattedPrice($prod->getPrice())),
			'image_url'=>($prod->getImageUrl()),
			'description'=>(getUTF8EncodedString($prod->getDescription())),
			'short_description'=>(getUTF8EncodedString($prod->getShortDescription())),
			'created'=>(getUTF8EncodedString(strtotime($prod->getCreatedAt())))
		);
	}
	return $mprds;
}


function getPhoneFormattedPrice($price)
{
	$price = (float)$price;
	$price = round($price, 2);
	$price *= 100;
	$price = (int)$price;
	return $price;
}
	
function getUTF8EncodedString($string)
{
	// if(mb_detect_encoding($string) != "UTF-8"){}{ $string = utf8_encode($string); }
	return $string;
}

logger("Starting: mygassi-jsonexport");
writeProductCatalogAsJSON();
logger("Done:  mygassi-jsonexport");
