<?php
require_once("mygassi-config.php");
require_once("mygassi-logger.php");
require_once(mcapi);
require_once(mageroot);

Mage::app();

logger("Starting: mygassi-mc-newsletter-campaign.php");

$coll = Mage::getModel("salesrule/rule")->getCollection();
$campId = null;
foreach($coll as $camp){
	print strpos("Newsletter", $camp->getName());
	if(false !== strpos($camp->getName(), "Newsletter")){
		$campId = $camp->getId();
	}
}

if(null === $campId){
	logger("No-Newsletter-Campaign");
	exit(1);
}

$code = null;

$coll = Mage::getModel("salesrule/coupon")->getCollection();
foreach($coll as $coupon){
	if($campId == $coupon->getRuleId()){
		$code = $coupon->getCode();
	}
}

switch($code)
{
	case null:
	case "":
		logger("No-Newsletter-Coupon");
		exit(1);
}


$api = new MCAPI(MCApiKey);
$api->useSecure(true); 
$type = 'regular';

$opts['list_id'] = NewsletterListId;
$opts['subject'] = 'Eur 5.- Rabatt für Empfängerinnen der MyGassi Newsletter!';
$opts['from_email'] = Mage::getStoreConfig("trans_email/ident_general/email"); 
$opts['from_name'] = Mage::getStoreConfig("general/store_information/name");
$opts['tracking']=array('opens' => true, 'html_clicks' => true, 'text_clicks' => false);
$opts['authenticate'] = true;
$opts['analytics'] = array('google'=>'my_google_analytics_key');
$opts['title'] = 'Eur 5,- Rabatt für Empfängerinnen der MyGassi Newsletter!';

$message = 'Herzlichen Glückwunsch zu Ihrem persönlichen Eur 5,- Newsletter-Rabatt-Coupon, einzulösen mit dem Gutschein "'.$code.'" bei Ihrem nächsten Einkauf im MyGassi Shop!';

$content = array(
	'html' => $message, 
	'text' => $message
);

/** OR we could use this:
$content = array('html_main'=>'some pretty html content',
		 'html_sidecolumn' => 'this goes in a side column',
		 'html_header' => 'this gets placed in the header',
		 'html_footer' => 'the footer with an *|UNSUB|* message', 
		 'text' => 'text content text content *|UNSUB|*'
		);
$opts['template_id'] = "1";
**/

$campaignId = $api->campaignCreate($type, $opts, $content);

if ($api->errorCode){
	echo "Unable to Create New Campaign!";
	echo "\n\tCode=".$api->errorCode;
	echo "\n\tMsg=".$api->errorMessage."\n";
} else {
	echo "New Campaign ID:".$campaignId."\n";
}


// on could do this; 
// it is probably a good idea 
// to click on that "send" button 
// in the  mailchimp panel
$retval = $api->campaignSendNow($campaignId);

if ($api->errorCode){
	echo "Unable to Send Campaign!";
	echo "\n\tCode=".$api->errorCode;
	echo "\n\tMsg=".$api->errorMessage."\n";
} else {
	echo "Campaign Sent!\n";
}

logger("Done: mygassi-mc-newsletter-campaign.php");

exit(1);
