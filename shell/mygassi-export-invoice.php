<?php

require_once("mygassi-config.php");
require_once("mygassi-logger.php");
require_once(mageroot);

// fetches a new mage instance
Mage::app();

// logs 
logger("Starting: mygassi-export-invoice");

// evaluates the order id
$orderID = $argv[1];

// exits without the id
if(null === $orderID){
	logger("No orderID");
	exit(1);
}

// fetches the order
$order = Mage::getModel("sales/order")->loadByIncrementId($orderID);

// print $order->getName();

// exits without an order
if(null === $order->getIncrementId()){
	logger("No such order: " . $orderID);
	exit(1);
}

// logs the given order
logger("order: " . $order->getIncrementId());

// fetches the invoices
$invoices = Mage::getResourceModel("sales/order_invoice_collection")->setOrderFilter($order->getId())->load();

// generates invoice PDF document
if($invoices->getSize() > 0){


	/***
	 * the coupon is weird thingy
	 * the coupon that is generated now is printed onto the invoice PDF
	 * the coupon code might get used at the *next order
	 * once the coupon code is used
	 * a new one gets generated before the invoice is getting generated
	 * for that the invoice got the *right coupon code for the next order
	 */
	// updates coupon code for the next order
	// once the *old coupon code is already used
	exec(UpdateCouponCommand . $order->getCustomerId());




	// writes the pdf document	
	if(!isset($pdf)){
		// sets up new pdf doc	
		$pdf = Mage::getModel("sales/order_pdf_invoice")->getPdf($invoices);
	} else {
		// adds a new pdf page
		$pages = Mage::getModel("sales/order_pdf_invoice")->getPdf($invoices);
		$pdf->pages = array_merge($pdf->pages, $pages->pages);
	}	
	// renders the pdf 
	$pdf->render();
	
	// saves a copy of a rendered pdf
	$customerID = $order->getCustomerId();
	$orderID = $order->getIncrementId();
	$date = date("Y-m-d_H-i-s");
	$path = PDFPath . $customerID . "_" . $orderID . "_invoice.pdf";
	switch($customerID){
		case "";
		case null:
		$path = PDFPath . "guest" . "_" . $orderID . "_invoice.pdf";
	}

	$pdf->save($path);
	
	// logs success
	logger("PDF saved: " . $path);
}
else { 
	logger("No invoice for sale: " . $order->getIncrementId()); 
}

exit(1);
