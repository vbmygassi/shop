<?php
require_once("mygassi-config.php");
require_once(mageroot);

Mage::app();

// $sales = Mage::getModel("sales/order")->getCollection()->addAttributeToFilter("status", "referred_to_karlie"); 
$sales = Mage::getModel("sales/order")->getCollection()->addAttributeToFilter("increment_id", "1200000020279");

foreach($sales as $sale){
	foreach ($sale->getInvoiceCollection() as $invoice) {
		$invoice->sendEmail();
	}
}

exit(1);

