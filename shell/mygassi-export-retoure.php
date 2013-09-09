<?php
require_once("mygassi-config.php");
require_once("mygassi-logger.php");
require_once(mageroot);
Mage::app();
logger("Starting: mygassi-export-retoure");
$orderID = $argv[1];
if(null === $orderID){
	logger("No orderID");
	exit(1);
}
$order = Mage::getModel("sales/order")->loadByIncrementId($orderID);
print $order->getName();
if(null === $order->getIncrementId()){
	logger("No such order: " . $orderID);
	exit(1);
}
logger("order: " . $order->getIncrementId());
$invoices = Mage::getResourceModel("sales/order_invoice_collection")->setOrderFilter($order->getId())->load();


// usual
if($invoices->getSize() > 0){
	if(!isset($pdf)){
		$pdf = Mage::getModel("sales/order_pdf_retoure")->getPdf($invoices);
	} else {
		$pages = Mage::getModel("sales/order_pdf_retoure")->getPdf($invoices);
		$pdf->pages = array_merge($pdf->pages, $pages->pages);
	}	
	// logger("About to render PDF");
	$pdf->render();
	$customerID = $order->getCustomerId();
	$orderID = $order->getIncrementId();
	$date = date("Y-m-d_H-i-s");
	$path = PDFPath . $customerID . "_" . $orderID . "_retoure.pdf";
	switch($customerID){
		case "";
		case null:
		$path = PDFPath . "guest" . "_" . $orderID . "_retoure.pdf";
	}

	// logger("About to save PDF");
	$pdf->save($path);
	logger("PDF saved: " . $path);
}
else { 
	logger("No retoure for sale: " . $order->getIncrementId()); 
}


// custom
/*
$customerID = $order->getCustomerId();
$orderID = $order->getIncrementId();
$date = date("Y-m-d_H-i-s");
$path = PDFPath . $customerID . "_" . $orderID . "_invoice.pdf";
switch($customerID){
	case "";
	case null:
	$path = PDFPath . "guest" . "_" . $orderID . "_invoice.pdf";
}
Mage::getModel("sales/order_pdf_invoice")->getCustomPdf($invoices, $path);
logger("Custom invoice: " . $order->getIncrementId()); 
logger("Done: mygassi-export-invoice");
*/


exit(1);
