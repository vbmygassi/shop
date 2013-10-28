<?php
require_once("mygassi-config.php");
require_once("mygassi-logger.php");
require_once(mageroot); 

Mage::app();

$coll = Mage::getModel("catalog/product")->getCollection();

$CSVBuff = '';
$CSVCommi = ", ";
$CVSEOL = "\n";

$i = 0;
foreach($coll as $index=>$product){
	$product = $product->load($product->getId());
	// print "index: " . $index . "\n"; 
	// print_r($product->debug());
	// $i++; 
	// print "i: " . $i . "\n";
	// CSV
	$CSVBuff .= '"' . $product->getSKU() . '"';
	$CSVBuff .= $CSVCommi;
	$CSVBuff .= '"' . $product->getName() . '"';
	$CSVBuff .= $CSVCommi;
	// $CSVBuff .= '"' . $product->getPrice() . '"'; 
	// $CSVBuff .= $CSVCommi;
	$prrrrice = Mage::helper("core")->currency(number_format($product->getPrice(), 2)); 
	$prrrrice = number_format($product->getPrice(), 2); 
	$CSVBuff .= '"' . $prrrrice . '"'; 
	$CSVBuff .= $CSVCommi;
	$prrrjice = str_replace(".", ",", $prrrrice); 
	$CSVBuff .= '"' . $prrrjice . '"'; 
	// 
	$CSVBuff .= $CSVEOL; 
}

print ($CSVBuff);
