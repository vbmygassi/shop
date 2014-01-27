<?php

require_once("mygassi-config.php");
require_once("mygassi-logger.php");
require_once("mygassi-send-email.php");

require_once(mageroot);

Mage::app();

/**
 * reads sales of state "pending" is the "new ones" 
 */
function getSales($state)
{
	$sales = Mage::getModel("sales/order")->getCollection()->addAttributeToFilter("status", $state); 
	if(null === $state){ 
		$sales = Mage::getModel("sales/order")->getCollection(); 
	}
	return $sales;
}

function getSale($id)
{
	$sales[]= Mage::getModel("sales/order")->load($id, "increment_id"); 
	return $sales;
}

function getTransactionId($customer, $sale)
{
	if(null === ($cid = $sale->getCustomerId())){ $cid = "guest"; }
	$res  = "";
	$res .= $cid;
	$res .= "_";
	$res .= $sale->getIncrementId(); 
	return $res;
}

/**
 * sets up the sales container
 */
function sendContainer($sales, $deletePDF)
{
	// loops through all selected sales
	foreach($sales as $sale){
		
		// skips "non existent" sales (dev issue: was: deprecated)
		if(null === ($customer = Mage::getModel("customer/customer")->load($sale->getCustomerId()))){
			continue;
		}

		// skips "premium" orders
		$cgid = $customer->getGroupId();
		$code = Mage::getSingleton("customer/group")->load($cgid)->getCustomerGroupCode();

		if("General" != $code){
			print $code . PHP_EOL;
			print $cgid . PHP_EOL;
			continue;
		}
		
		// Gerdt Vladimir @ Karli (and fills)
		// 
		$shipaddr       = $sale->getShippingAddress();
		// 
		$firstName 	= $shipaddr->getFirstname();
		$lastName	= $shipaddr->getLastname();
		$email 		= $shipaddr->getEmail();
		$telephone 	= $shipaddr->getTelephone();
		$street 	= implode(" ", $shipaddr->getStreet());
		$country 	= $shipaddr->getCountry();
		$city 		= $shipaddr->getCity();
		$postcode 	= $shipaddr->getPostcode();
		///////  
		$kvg_arr_order = array();
		$kvg_lfdnr = getTransactionId($customer, $sale);			// Eindeutige Auftragsnummer
		/////// Kopfdaten
		$kvg_arr_order[$kvg_lfdnr]['key'] = $kvg_lfdnr;				// Eindeutige Auftragsnummer
		$kvg_arr_order[$kvg_lfdnr]['order_number'] = $sale->getIncrementId();	// Bestellnummer, optional
		$kvg_arr_order[$kvg_lfdnr]['customer_note'] = $sale->getCustomerNote(); // Kommentar zum Auftrag max. 200 Zeichen, optional
		
		$d = DateTime::createFromFormat("Y-m-d H:i:s", $sale->getCreatedAt());
		$createdAt = $d->format("Y-m-d H:i");
		$createdAtDate = $d->format("Y-m-d");
		$createdAtTime = $d->format("H:i");
		
		$kvg_arr_order[$kvg_lfdnr]['order_date'] = $createdAtDate;	// Auftragsdatum
		$kvg_arr_order[$kvg_lfdnr]['order_time'] = $createdAtTime;	// Auftragszeit
		
		/////// Kundendaten
		$kvg_arr_order[$kvg_lfdnr]['customer_first_name'] = $firstName;		// Vorname
		$kvg_arr_order[$kvg_lfdnr]['customer_last_name'] = $lastName;		// Nachname
		$kvg_arr_order[$kvg_lfdnr]['customer_county'] = $country;		// Land
		$kvg_arr_order[$kvg_lfdnr]['customer_city'] = $city;			// Stadt
		$kvg_arr_order[$kvg_lfdnr]['customer_postal_code'] = $postcode;		// Postleitzahl
		$kvg_arr_order[$kvg_lfdnr]['customer_street']  = $street;		// Strasse
		$kvg_arr_order[$kvg_lfdnr]['customer_phone'] = $telephone;		// Telefon
		$kvg_arr_order[$kvg_lfdnr]['customer_mobile'] = $mobile;		// Mobile Telefon
		$kvg_arr_order[$kvg_lfdnr]['customer_fax'] = $fax;			// Fax
		$kvg_arr_order[$kvg_lfdnr]['customer_email'] = $email;			// Email
		/////// Produkte
		foreach($sale->getAllItems() as $item){
			$articleID = $item->getSku();
			$kvg_arr_order[$kvg_lfdnr]['position'][$articleID]['article_number'] = $articleID;    // Artikelnummer
			$kvg_arr_order[$kvg_lfdnr]['position'][$articleID]['amount'] = $item->getQtyOrdered();     // Menge
			$kvg_arr_order[$kvg_lfdnr]['position'][$articleID]['price'] = $item->getPrice();           // Preis
			$i++;
		}

		// -->

		


		//////
		// writes the invoice PDF document
		exec(ExportInvoiceCommand . $sale->getIncrementId());
		
		// writes the retoure PDF document
		exec(ExportRetoureCommand . $sale->getIncrementId());
	
		// 	
		$invoicePDF = PDFPath . $kvg_lfdnr . "_invoice.pdf";
		$retourePDF = PDFPath . $kvg_lfdnr . "_retoure.pdf";
		
		// checks invoice PDF; skips the sale without an invoice
		if(!file_exists($invoicePDF)){
			
			// logs the fail
			logger("No Invoice. Will not send the order: " . $kvg_lfdnr);
			
			// fills up CRON notification email (administrative issue)
			EmailNotification::add("<span style='color:red'>Bestellung $kvg_lfdnr konnte wegen der fehlenden Quittung nicht zu Karlie gesandt werden.</span>");
			
			// skips the sale
			continue;
		}
		
		// sends the order to karlie
		if("true" === sendOrder($kvg_arr_order, $invoicePDF, $retourePDF)){
		
			// writes DB record (order is referred to karlie)	
			$sale->setKarlieOrderId($kvg_lfdnr);
			$sale->setState("processing", true, "Die Bestellung ist zu Karlie weitergeleitet. ". $sale->getKarlieOrderId());
			$sale->setStatus("referred_to_karlie");
			$sale->save();
		
			// logs the CRON success	
			logger("Order sent to Karlie");

			// fills up CRON notification email (administrative issue)
			EmailNotification::add("<span class='green'>Bestellung $kvg_lfdnr ist zu Karlie gesandt worden.</span>");
	
			// sends customer notification email 
			foreach ($sale->getInvoiceCollection() as $invoice) {
				try{ 
					$invoice->sendEmail(); 
				}
				catch(Exception $e){
					logger("Could not sent Customer Notification Email: " . $e);
				}
			}
		} 
		else {

			// logs send fail 
			logger("Could not send the order");
			
			// fills up CRON notification email (administrative issue)
			EmailNotification::add("<span class='red'>Bestellung $kvg_lfdnr konnte nicht zu Karlie gesandt werden.</span>");
		}
	}
}

function formatPrice($value)
{
	return Mage::helper('core')->currency($value);	
}














// Gerdt Vladimir @ Karlie 
function sendOrder($kvg_arr_order, $invoicePath, $retourePath)
{
	////////////// ab hier nichts aendern //////////////

	// Auftragsarray als JSON Format initialisieren
	$kvg_arr_invoice_json = json_encode( $kvg_arr_order );

	// REST POST
	// URL
	// $request = 'http://csdaten.karlie.de/Git/magento-project-karlie/webservice/public/index.php/v1/order';
	// $request = 'http://10.14.10.20/Git/magento-project-karlie/webservice/public/index.php/v1/order';
	$request = OrderPath;
	// Auftragsarray und Applikation, welches ueber POST an die URL uebergeben wird
	$postargs = array
	(
    		'appid' => 2,
    		'args' => urlencode( $kvg_arr_invoice_json ),
    		'rechnungen' => '@' . $invoicePath, // @ ist erforderlich am Anfang. Beispiel fuer PDF Rechnungen, Serverpfad
    		'retouren' => '@' . $retourePath // @ ist erforderlich am Anfang. Beispiel fuer PDF Rechnungen, Serverpfad
	);
	logger($postargs);

	// headers fuer JSON setzten
	$headers = array(
    		'Accept: application/json',
    		'Content-Type: application/json'
	);

	// mit curl die REST - Schnittstelle ueber POST ansprechen
	$handle = curl_init();
	curl_setopt($handle, CURLOPT_URL, $request);
	curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);

	curl_setopt($handle, CURLOPT_POST, true); // POST
	curl_setopt($handle, CURLOPT_POSTFIELDS, $postargs);

	$response = curl_exec($handle); // ausfuehren und Callback in $response
	$code = curl_getinfo($handle, CURLINFO_HTTP_CODE); // Status Code

	// close cURL resource, and free up system resources
	curl_close($handle);
	echo $code . '<br />';
	echo $response;

	return $response;	
}














    
/**
 * "pending" flagged sales is the *new sales
 *
 * usage: 
 * php mygassi-salesexport.php
 * php mygassi-salesexport.php referred_to_karlie
 * php mygassi-salesexport.php payed
 * php mygassi-salesexport.php payed [123 -num of sale]
 */

if(null === ($salesState = $argv[1])){ 
	$salesState = "payed"; 
}

EmailNotification::setSubject("Benachrichtigung über den MyGassi CRON Prozess Salesexport");

if(isset($argv[2])){
	EmailNotification::add("<span style='color:green'>Der Versand der Bestellung " . $salesState . " (" . $argv[2] . ") ist gestartet.</span>");
	logger("Starting: mygassi-salesexport: " . $salesState . " : " . $argv[2]);
	sendContainer(getSale($argv[2]));
}
else { 
	EmailNotification::add("<span style='color:green'>Der Versand der Bestellungen (" . $salesState . ") ist gestartet.</span>");
	logger("Starting: mygassi-salesexport: " . $salesState);
	sendContainer(getSales($salesState));
}

EmailNotification::add("<span style='color:green'>Der Versand der Bestellungen ist beendet.</span>");
EmailNotification::send();

logger("Done: mygassi-salesexport");

exit(1);
