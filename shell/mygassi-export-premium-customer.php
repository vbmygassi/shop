<?php
/********
 Admin-URL: http://ec2-54-246-38-175.eu-west-1.compute.amazonaws.com:4000
 Backend-URL: http://ec2-54-246-38-175.eu-west-1.compute.amazonaws.com:3001
 Premium-URL: http://ec2-54-246-38-175.eu-west-1.compute.amazonaws.com:3000

 ******************************/

require_once("mygassi-config.php");
require_once("mygassi-logger.php");
require_once(mageroot);

logger("Starting: mygassi-export-premium-customer");

Mage::app();

$authKey = "magento";

/*
$service = "http://ec2-54-246-38-175.eu-west-1.compute.amazonaws.com:3001/poi/premiumcategories";
$handle = curl_init();
curl_setopt($handle, CURLOPT_URL, $service);
curl_setopt($handle, CURLOPT_POST, false); 
curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($handle);
$code = curl_getinfo($handle, CURLINFO_HTTP_CODE);
curl_close($handle);
print "response: " . $response . PHP_EOL;
print "code: " . $code . PHP_EOL;
exit(1);
*/

$service = "http://ec2-54-246-38-175.eu-west-1.compute.amazonaws.com:4000/premium_import_magento";
$coll = Mage::getModel("customer/customer")->getCollection();
foreach($coll as $customer){
	$cgid = $customer->getGroupId();
	$code = Mage::getSingleton("customer/group")->load($cgid)->getCustomerGroupCode();
	if("Premium" == $code){
		$postargs = array(
			"premium_poi_type"=>"gold",
			"mail"=>"vb@mygassi.com",
			"send_mail"=>"false",
			"premium_poi_category"=>"no-such-category",
			"name"=>"totally-awesome-software-inc"	
		);
		// $postargs = json_encode($postargs);
		print_r($postargs);
		print PHP_EOL;
		$handle = curl_init();
		curl_setopt($handle, CURLOPT_URL, $service);
		curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($handle, CURLOPT_POST, true); 
		curl_setopt($handle, CURLOPT_POSTFIELDS, $postargs);
		curl_setopt($handle, CURLOPT_HTTPHEADER, array("Authorization: " . $authKey, "User-Agent: " . $authKey));
		$response = curl_exec($handle);
		$code = curl_getinfo($handle, CURLINFO_HTTP_CODE);
		curl_close($handle);
		print "response: " . $response . PHP_EOL;
		print "code: " . $code . PHP_EOL;
	}
}

logger("Done: mygassi-export-premium-customer");
exit(1);
