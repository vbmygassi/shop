<?php 

function prompt($message)
{
	print $message;
	$handle = fopen("php://stdin", "r");
	$line = fgets($handle);
	if("ja" !== trim($line)){
		print "Dann nicht.\n";
		exit();
	}
}
prompt("Sind Sie Sich über die Folgen Ihrer Handlung im Klaren?\n");
prompt("Wirklich?\n");

print "Nee, mache ich nicht.\n";
exit(1);

require_once("mygassi-config.php");
require_once(mageroot);
Mage::app('admin')->setUseSessionInUrl(false);                                                                                                                 
$collection = Mage::getModel("sales/order")->getCollection();
foreach($collection as $order){
	try{
		Mage::getModel('sales/order')->loadByIncrementId($order->getIncrementId())->delete();
		print "order #".$order->getIncrementId()." is removed".PHP_EOL;
	}
	catch(Exception $e){
		print "order #".$order->getId()." could not be remvoved: ".$e->getMessage().PHP_EOL;
	}
}
print "complete";
print "n";

?>
