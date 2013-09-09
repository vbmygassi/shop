<?php
/**
 * Imports downloaded product image assets from the "import" b(f)ucket (folder)
 * (that is where product image assets get rsync'd 
 */
require_once("mygassi-config.php");
require_once("mygassi-logger.php");
require_once(mageroot); 

Mage::app();

logger("Starting: mygassi-download-assets"); 

$coll = Mage::getModel("catalog/product")->getCollection();

foreach($coll as $prod)
{
	$prod = $prod->load($prod->getId());
	$name = $prod->getImage();
	if(null === $name){ 
		return; 
	}
	$name = str_replace("magpic/", "", $name);
	$name = str_replace("/", "", $name);
	$dest = Mage::getBaseDir("media") . DS . "import" . DS . $name;
	$target = MygassiImportImagePath . $name;
	print "from: " . $target . "\n";
	print "dest: " . $dest . "\n";
	logger("fetching: " . $target);
	logger("to:       " . $dest);
	$fp = fopen($dest, "w");
	$ch = curl_init($from);
	curl_setopt($ch, CURLOPT_TIMEOUT, 120);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_FILE, $fp);
	if($res = curl_exec($ch)){
		print "download did\n";
	} else {
		print "could not download: " . $target . "\n";
		$res = curl_close($ch);
		fclose($fp);
		continue;
	}
	$res = curl_close($ch);
	fclose($fp);
}

logger("Done: mygassi-download-assets"); 

exit(1);
