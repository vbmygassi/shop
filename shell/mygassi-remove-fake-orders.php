<?php
function prompt($message)
{
	print $message;
	$handle = fopen("php://stdin", "r");
	$line = fgets($handle);
	if("ja" !== trim($line)){
		print "Dann nicht.\n";
		exit();
	}
}
prompt("Sind Sie Sich über die Folgen Ihrer Handlung im Klaren?\n");
prompt("Wirklich?\n");

// print "Nee, mache ich nicht.\n";
// exit(1);

require_once("mygassi-config.php");
require_once(mageroot);
Mage::app('admin')->setUseSessionInUrl(false);                                                                                                                 

$sales = Mage::getModel("sales/order")->getCollection()->addAttributeToFilter("status", "holded"); 
// $sales = Mage::getModel("sales/order")->getCollection(); 

foreach($sales as $sale){
	try{
		print "sale: ". $sale->getIncrementId()." is removed".PHP_EOL;
		Mage::getModel('sales/order')->loadByIncrementId($sale->getIncrementId())->delete();
	}
	catch(Exception $e){
		print "sale ".$sale->getId()." could not be remvoved: ".$e->getMessage().PHP_EOL;
	}
}

exit(1);
