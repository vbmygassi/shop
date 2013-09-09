<?php
require_once("mygassi-config.php");
require_once(mageroot);

Mage::app();

$sales = Mage::getModel("sales/order")->getCollection()->addAttributeToFilter("increment_id", "1200000020279"); 

foreach($sales as $sale){
	foreach ($sale->getInvoiceCollection() as $invoice) {
		print_r($invoice->debug());
		continue;
		$invoice->sendEmail();
	}
}

exit(1);
