<?php
require_once("mygassi-logger.php");
require_once("mygassi-config.php");
require_once("mygassi-send-email.php");
require_once(mageroot);
Mage::app();
logger("Starting mygassi-retoure.php");
EmailNotification::setSubject("Benachrichtigung über den MyGassi CRON Prozess Retoure");
EmailNotification::add("<span style='color:black'>Der Retoure CRON Prozess startet.</span>");
$coll = Mage::getModel("sales/order")->getCollection()->addAttributeToFilter("state", "sent");
foreach($coll as $sale){
	$target = RetMridPath . $sale->getIncrementId();
	logger($target);
	EmailNotification::add("<span style='color:green'>Bestellung: " . $sale->getIncrementId() . "</span>");
	$ch = curl_init($target);
	curl_setopt($ch, CURLOPT_TIMEOUT, 120);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_FILE, $fp);
	curl_exec($ch);
	curl_close($ch);
	fclose($fp);
	$res = file_get_contents(RetMridPath . $sale->getIncrementId());
	unlink(RetMridPath . $sale->getIncrementId());
	switch($res){
		case null:
		case "":
		case "null":
		case "[]":
		continue;
	}
	$arry = json_decode($res);
	$obj = $arry[0];
	$sale = Mage::getModel("sales/order")->loadByIncrementId($obj->bestellnr);
	$sale = $sale->load($sale->getId());
	$flag = $sale->getStatus();
	if(null !== $sale->getID()){
		print "sale: " . $sale->getId() . " : " . $flag . "\n";
		print "status:" . $sale->getStatus() . "\n";
		print "state:" . $sale->getState() . "\n";
		$sale->setStatus("retoure")->save();
		$sale->setState("processing", true, "Die Bestellung wird zurückgesendet.". $sale->getKarlieOrderId());
		logger("Die Bestellung:" . $sale->getKarlieOrderId() . " wird zurückgesendet");
		EmailNotification::add("<span style='color:green'>Die Bestellung " . $sale->getKarlieOrderId() . " wird zurückgesendet</span>");
	}
}
EmailNotification::add("<span style='color:black'>Der Retoure CRON Prozess endet.</span>");
EmailNotification::send();
logger("Done mygassi-retoure.php");
exit(1);
