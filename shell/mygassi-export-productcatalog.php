<?php



require_once("mygassi-config.php");
require_once(mageroot);
Mage::app();
Mage::getModel("jsonexport/catalog")->writeProductCatalog();

exit(1);

$tempfile = JSONExportPath;
$url = JSONServiceURL;
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6");
curl_setopt($ch, CURLOPT_TIMEOUT, 60);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 0);
$fp = fopen($tempfile, "w");
curl_setopt($ch, CURLOPT_FILE, $fp);
curl_setopt($ch, CURLOPT_POST, 0);
$result = curl_exec($ch); 
fclose($fp);
/*
$stream = file_get_contents($tempfile);
unlink($tempfile);
$arri = json_decode($stream);
foreach($arri as $export){
	foreach($export as $field){
		// the strings are utf-8 basically
		// but than the import db delivers garbage
		$temp = $field;	
		$temp = str_replace("Ã¼", "ü", $temp);
		$temp = str_replace("Ã¤", "ä", $temp);
		$temp = str_replace("Ã", "ß", $temp);
		$temp = str_replace("Ã", "ß", $temp);
		$temp = str_replace("", "", $temp);
		print $temp; 
		print "\n";
	}	
}
*/
exit(1);
