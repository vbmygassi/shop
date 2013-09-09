<?php
/**
This Example shows how to run a Batch Subscribe on a List using the MCAPI.php 
class and do some basic error checking or handle the return values.
**/
require_once '/Users/vico/Workspace/MyGassiShop/shell/mailchimp-api-class/MCAPI.class.php';

$apikey = "94a46f6cef53348a7261f5e5c0014448-us7";

$api = new MCAPI($apikey);
$listId = "998ef02c1a";
$listId = "f96f899e42";

$batch[] = array('EMAIL'=>"vb@mygassi.com", 'FNAME'=>'Viktor', 'LNAME'=>'Berzsinszky');
$batch[] = array('EMAIL'=>"vberzsin@gmail.com", 'FNAME'=>'Viktor', 'LNAME'=>'Berzsinszky');
$batch[] = array('EMAIL'=>"vberzsin@yahoo.com", 'FNAME'=>'Viktor', 'LNAME'=>'Berzsinszky');

$optin = true; //yes, send optin emails
$up_exist = true; // yes, update currently subscribed users
$replace_int = false; // no, add interest, don't replace

$vals = $api->listBatchSubscribe($listId,$batch,$optin, $up_exist, $replace_int);

if ($api->errorCode){
    echo "Batch Subscribe failed!\n";
	echo "code:".$api->errorCode."\n";
	echo "msg :".$api->errorMessage."\n";
} else {
	echo "added:   ".$vals['add_count']."\n";
	echo "updated: ".$vals['update_count']."\n";
	echo "errors:  ".$vals['error_count']."\n";
	foreach($vals['errors'] as $val){
		echo $val['email_address']. " failed\n";
		echo "code:".$val['code']."\n";
		echo "msg :".$val['message']."\n";
	}}
?> 


