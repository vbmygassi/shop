<?php

define('MAGENTO_ROOT', getcwd());

$mageFilename  = MAGENTO_ROOT . '/app/Mage.php';
require_once $mageFilename;
Mage::app();

$state = "pending";
$state = "processing";
$state = "referred_to_karli";
$state = "new";

$coll = Mage::getModel("sales/order")->getCollection()->addAttributeToFilter("status", $state);

foreach($coll as $sale){
	print "sale: ";
	print $sale->getStatus();
	print "\n";
}

