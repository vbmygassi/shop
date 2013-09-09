<?php
/**
 * Command an den Checkout Success gebunden
 * Generiert ein PDF Dokument
 * Könnte man machen doch warum sollte man so etwas tun
 */
class Mygassi_Pdfwriter_Model_Observer
{
	public function generateInvoicePDF($observer)
	{ 
		foreach($observer->getOrderIds() as $index=>$value){ 
			$orderID = $value; 
		}
		if(null !== ($order = Mage::getModel("sales/order")->load($orderID))){
			$invoice = Mage::getResourceModel('sales/order_invoice_collection')->setOrderFilter($orderID)->load();
			$pdf = Mage::getModel("sales/order_pdf_invoice")->getPdf($invoice);
			$file = $pdf->render();
			var_dump($file);
		}
	}
}
