<?php
require_once("mygassi-config.php");
require_once("mygassi-logger.php");
require_once(mageroot);
Mage::app();
logger("Starting: mygassi-export-all-invoices");
$coll = Mage::getModel("sales/order")->getCollection();
foreach($coll as $order){
	logger("order: " . $order->getIncrementId());
	// exec("php /home/vberzsin/shell3/mygassi-export-invoice.php " . $order->getIncrementId());
	exec(ExportInvoiceCommand . $order->getIncrementId());
	/*
	$invoices = Mage::getResourceModel("sales/order_invoice_collection")->setOrderFilter($order->getId())->load();
	if($invoices->getSize() > 0){
		if(!isset($pdf)){
			$pdf = Mage::getModel("sales/order_pdf_invoice")->getPdf($invoices);
		} else {
			$pages = Mage::getModel("sales/order_pdf_invoice")->getPdf($invoices);
			$pdf->pages = array_merge($pdf->pages, $pages->pages);
		}	
		// logger("About to render PDF");
		$pdf->render();
		$customerID = $order->getCustomerId();
		$orderID = $order->getIncrementId();
		$date = date("Y-m-d_H-i-s");
		$path = PDFPath . $customerID . "_" . $orderID . "_invoice.pdf";
		// logger("About to save PDF");
		$pdf->save($path);
		logger("PDF saved: " . $path);
	}
	else { logger("No invoice for sale: " . $order->getIncrementId()); }
	*/
}
logger("Done: mygassi-export-all-invoices");
exit(1);
