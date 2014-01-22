<?php

/************

 Mockup of a "send invoice email" workflow [from !!localhost]
		// --> take care of not spamming ....
		
	[ mockup of a sales transmission to karlie ]
	
	-> selects a given order
	-> generates a new coupon
	-> generates a pdf doc
	-> sends the "invoice" email (and a pdf attachment)
		to the customers email address

 */
 

require_once("mygassi-config.php");
require_once(mageroot);
Mage::app();

// THE sale
// $saleId = "1200000020318";
$saleId = "1200000020319";

// selects the given sale
$sales = Mage::getModel("sales/order")->getCollection()->addAttributeToFilter("increment_id", $saleId);

foreach($sales as $sale){
	foreach($sales as $sale){
		print "about to set status of order to 'sent_to_karlie'\n";
		$sale->setStatus("referred_to_karlie");
		$sale->save();
	}
}

foreach($sales as $sale){
	// generates pdf (and a coupon)
	foreach($sales as $sale){
		print "about to generate the 'coupon' and the 'PDF'\n";
		exec(ExportInvoiceCommand . $sale->getIncrementId());
	}
	// sends the mail 
	foreach ($sale->getInvoiceCollection() as $invoice) {
		print "about to send the mail\n";
		$invoice->sendEmail();
	}
}

print "done\n";

exit(1);

