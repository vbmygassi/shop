<?php
require_once("mygassi-config.php");
print "fetching: " . JSONServiceURL . "\n";
$ch = curl_init(JSONServiceURL);
curl_setopt($ch, CURLOPT_URL, JSONServiceURL);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6");
curl_setopt($ch, CURLOPT_TIMEOUT, 60);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 0);
curl_setopt($ch, CURLOPT_COOKIEFILE, CookiePath);
curl_setopt($ch, CURLOPT_COOKIEJAR, CookiePath);
$fp = fopen(TempfilePath, "w");
curl_setopt($ch, CURLOPT_FILE, $fp);
curl_setopt($ch, CURLOPT_POST, 0);
$result = curl_exec($ch); 
fclose($fp);
$stream = file_get_contents(TempfilePath);
// unlink(TempfilePath);
try{ $arri = json_decode($stream); }
catch(Exception $e){ print "Some exception while decoding json: " . $e; }
foreach($arri as $export){ foreach($export as $field){
	$temp = $field;	
	$temp = str_replace("Ã¼", "ü", $temp);
	$temp = str_replace("Ã¤", "ä", $temp);
	$temp = str_replace("Ã", "ß", $temp);
	$temp = str_replace("Ã", "ß", $temp);
	$temp = str_replace("", "", $temp);
	print $temp; 
	print "\n";
}}
exit(1);
