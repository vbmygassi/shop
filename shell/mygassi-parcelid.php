<?php
require_once("mygassi-config.php");
require_once("mygassi-logger.php");
require_once(mageroot);

Mage::app();

logger("Starting: mygassi-parcelid");

/**
 * sets up a new client
 */
$client = Mage::getModel('codex_api/api');

/**
 * reads packet id from input
 */
$in = $argv[1]; 
if("" === $in){
	$in = "MG_mygassi_100000001";
}
$temp = explode("_", $in);
if(2 != count($temp)){
	logger("usage: php mygassi-parcelid.php guest_10001");
	print  "usage: php mygassi-parcelid.php guest_10001";
	print "\n";
	exit();
}
$customerID = $temp[0];
$orderID = $temp[1];

$target = ParcelTrackPath . $customerID . "_" . $orderID; 
logger("fetching: " . $target);
$fp = fopen(TempfilePath . $customerID . "_" . $orderID, "w");
$ch = curl_init($target);
curl_setopt($ch, CURLOPT_TIMEOUT, 120);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_FILE, $fp);
curl_exec($ch);
curl_close($ch);
fclose($fp);

/**
 * selects sale
 */
$sale = Mage::getModel("sales/order")->loadByIncrementId($orderID);
if(null === $sale){
	logger("No such sale");
	exit(1);
}

/** 
 * reads curl result
 */
$parcelID = file_get_contents(TempfilePath . $customerID . "_" . $orderID);
unlink(TempfilePath . $customerID . "_" . $orderID);
print "parcelID: " . $parcelID . "\n";
switch($parcelID){
	case null:
	case "null":
	case "[]":
		logger("No ParcelID: " . $target);
		logger("Done: mygassi-parcelid");	
		exit();
}
// unlink(Tempfilepath . $target);
//create shipment
/*
$itemQty =  $sale->getItemsCollection()->count();
$shipment = Mage::getModel('sales/service_order', $sale)->prepareShipment($itemQty);
$shipment = new Mage_Sales_Model_Order_Shipment_Api();
$shipmentId = $shipment->create( $sale->getIncrementId(), array(), 'Shipment created through ShipMailInvoice', true, true);

//add tracking info
$shipment_collection = Mage::getResourceModel('sales/order_shipment_collection');
$shipment_collection->addAttributeToFilter('order_id', $orderID);
foreach($shipment_collection as $sc)
{
	$shipment = Mage::getModel('sales/order_shipment');
	$shipment->load($sc->getId());
	if($shipment->getId() != ''){
		try{
			Mage::getModel('sales/order_shipment_track')
				->setShipment($shipment)
				->setData('title', 'carrier')
				->setData('number', $trackInfo)
				->setData('carrier_code', 'custom')
				->setData('order_id', $shipment->getData('order_id'))
				->save();
		}
		catch (Exception $e){
			Mage::getSingleton('core/session')->addError('order id '.$orderID.' no found');
		}
	}
}
*/
/****
 * writes shipment | lieferschein
 */
if($sale->canShip()){
	logger("Sale can ship");
	try {
		$shipment = Mage::getModel('sales/service_order', $sale)->prepareShipment(getItemQtys($sale));
		$shipmentCarrierCode = 'Parcel Code';
		$shipmentCarrierTitle = 'Parcel Title';
		$shipmentTrackingNumber = $parcelID;
		$arrTracking = array(
			'carrier_code' => isset($shipmentCarrierCode) ? $shipmentCarrierCode : $sale->getShippingCarrier()->getCarrierCode(),
			'title' => isset($shipmentCarrierTitle) ? $shipmentCarrierTitle : $sale->getShippingCarrier()->getConfigData('title'),
			'number' => $shipmentTrackingNumber
		);
		$track = Mage::getModel('sales/order_shipment_track')->addData($arrTracking);
		$shipment->addTrack($track);
		$shipment->register();
		$shipment->getOrder()->setIsInProcess(true);
		$transactionSave = Mage::getModel('core/resource_transaction')
				->addObject($shipment)
				->addObject($sale)
				->save();
		$emailSentStatus = $shipment->getData('email_sent');
		if (!is_null($customerEmail) && !$emailSentStatus) {
			$shipment->sendEmail(true, $customerEmailComments);
			$shipment->setEmailSent(true);
		}
		// $sale->setState("sent", true, "ParcelID: " . $parcelID);
		// $sale->setState("complete", true, "ParcelID: " . $parcelID);
		// $sale->setStatus("Complete", true, "ParcelID: " . $parcelID);
		$sale->load($sale->getId())->setState("sent", "Karlie hat die Ware versandt");
		$sale->load($sale->getId())->setStatus("Versandt");
		$sale->setParcelID($parcelID);
		$sale->save();
	} 
	catch (Exception $e) {
        	logger("Some exception while adding shipment " . $e->getMessage());
	}
}
function getItemQtys(Mage_Sales_Model_Order $sale)
{
	$qty = array();
	foreach ($sale->getAllItems() as $_eachItem) {
		if ($_eachItem->getParentItemId()) {
			$qty[$_eachItem->getParentItemId()] = $_eachItem->getQtyOrdered();
		}
		else {
			$qty[$_eachItem->getId()] = $_eachItem->getQtyOrdered();
		}
	}
	return $qty;
}
logger("Done: mygassi-parcelid");
exit(1);
