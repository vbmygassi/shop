<?php

require_once("mygassi-config.php");
require_once("mygassi-logger.php");
require_once("mygassi-send-email.php");
require_once(mageroot);

// a running mage client 
Mage::app();

// logs the start
logger("Starting: mygassi-check-parcels");

// fills up notification email (administrative issue);
EmailNotification::setSubject("Benachrichtigung über den MyGassi CRON Prozess Get Parcel ID");
EmailNotification::add("<span style='color:black'>Der CRON Job Check Parcel ID startet.</span>");

// selects sales (the ones that are referred to karlie)
$sales = Mage::getModel("sales/order")->getCollection()->addAttributeToFilter("status", "referred_to_karlie"); 

// loops through the sales and fetches parcel id of each sale
foreach($sales as $sale){
	
	// loads the sale
	$sale = $sale->load($sale->getId());
	
	// loads order id of current sale
	$orderID = $sale->getIncrementId();
	
	// loads customer id of the current sale
	$customerID = $sale->getCustomerId();
	
	// customer of current sale might be a guest
	if(null === $customerID){ $customerID = "guest"; }
	
	// loads parcel id of the sale (from karlie)	
	$message = exec(GetParcelIDCommand . $customerID . "_" . $orderID);

	// fills up CRON notification email
	$color = "green";
	switch($message){
		case "":
		case "null":
		case "parcelID: null":
		case null:
			$color = "red";
			break;
	}
	
	EmailNotification::add("<span style='color:$color'>Bestellung: ".$customerID."_".$orderID." $message</span>");
}

// sends CRON notification email
EmailNotification::add("<span style='color:black'>Der CRON Job Check Parcel ID endet.</span>");
EmailNotification::send();

// logs done message
logger("Done: mygassi-check-parcels");

exit(1);
