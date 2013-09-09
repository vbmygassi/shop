<?php
require_once("mygassi-config.php");

$lh = null; 
function logger($message){
	global $lh;
	// if(null === $lh){ $lh = fopen(LogPath, "w+"); }
	if(null === $lh){ $lh = fopen(LogPath, "a+"); }
	fwrite($lh, microtime());
	fwrite($lh, " : ");
	fwrite($lh, $message);
	fwrite($lh, "\n");
	return true;
}

 
