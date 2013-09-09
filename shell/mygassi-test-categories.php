<?php
require_once("mygassi-config.php");
require_once(mageroot);
Mage::app();

$test = Mage::getModel("catalog/category")->load(117436);
var_dump($test->debug());
$test = Mage::app()->getStores();
foreach($test as $t){
	print $t->getId();
	print "\n";
}
exit();

$coll = Mage::getModel("catalog/product")->getCollection();
$missing = array();

foreach($coll as $product){
	$product = $product->load($product->getId());
	$catIDs = $product->getCategoryIds();
	print $product->getSku();
	print " : ";
	print $product->getName();
	print "\n";
	print "categoryIds: " . implode(",", $catIDs);
	print "\n";
	foreach($catIDs as $catID){ 
		$cat = null;
		$cat = Mage::getModel("catalog/category")->load($catID);
		if(null === $cat->getId()){
			print "MSSING CATEGORY: " . $catID . "\n";
			$missing[]= new MissingCategoryEntry($catID, $product->getId());
		} 
		else {
			print "cat: " . $cat->getId() . ":" . $cat->getName();
			print "\n";
		}
	}
	print "----";
	print "\n";
}

if(count($missing) > 1){
	print "Test failed: there is some missing categories.\n";
	foreach($missing as $mce){
		var_dump($mce);
		/*
		print "categoryID: " . $entry->catID;
		print " | ";
		print "productID: " . $entry->productID; 
		print "\n";
		*/
	}
} 
else {
	print "**********************************************";
	print "\n";
	print "Test succeeded: there is no missing categories\n";
	print "\n";
	print "\n";
	print "\n";
	print "\n";
}

class MissingCategoryEntry
{
	var $catID; 
	var $productID;
	
	public function __construct($catID, $productID){
		$this->catID = $catID;
		$this->productID = $productID;	
	}
}
