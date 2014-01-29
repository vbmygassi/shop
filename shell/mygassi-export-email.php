<?php
require_once("mygassi-config.php");
require_once("mygassi-logger.php");
require_once(mageroot);

logger("Starting: mygassi-export-email");

Mage::app();

$emails = array(""); 

$coll = Mage::getModel("customer/customer")->getCollection();
foreach($coll as $customer){
	$cgid = $customer->getGroupId();
	$code = Mage::getSingleton("customer/group")->load($cgid)->getCustomerGroupCode();
	if("General" == $code){
		$email = '"' . $customer->getEmail() . '"';
		if(!in_array($email, $emails)){ array_push($emails, $email); }
	}
}

$coll = Mage::getModel("sales/order")->getCollection();
foreach($coll as $sale){
	$email = '"' . $sale->getShippingAddress()->getEmail() . '"';
	if(!in_array($email, $emails)){ array_push($emails, $email); }
	$email = '"' . $sale->getBillingAddress()->getEmail() . '"';
	if(!in_array($email, $emails)){ array_push($emails, $email); }
}

$emails[0] = "Email";
$buff .= implode("\n", $emails);


print($buff);

logger("Done: mygassi-export-email");
exit(1);
