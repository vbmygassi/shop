<?php
require_once("mygassi-config.php");
require_once(mageroot);

Mage::app();

$sale = Mage::getModel("sales/order")->load("1200000020268", "increment_id"); 
$sale = $sale->load($sale->getId());

print "status: " . $sale->getStatus() . "\n";
print "state: " . $sale->getState() . "\n";

exit(1);

