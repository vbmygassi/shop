<?php
/**
 * Imports downloaded product image assets from the "import" b(f)ucket (folder)
 * (that is where product image assets get rsync'd 
 */
require_once("mygassi-config.php");
require_once("mygassi-logger.php");
require_once(mageroot); 

Mage::app();

logger("Starting: mygassi-import-assets"); 

$coll = Mage::getModel("catalog/product")->getCollection();

foreach($coll as $prod)
{
	$prod = $prod->load($prod->getId());
	$name = $prod->getImage();
	if(null === $name){
		print "no name\n";
		continue;
	}
	try{
		$media = Mage::getModel("catalog/product_attribute_media_api");
		$items = $media->items($prod->getId());
		foreach($items as $item){
			$fp = Mage::getBaseDir("media") . DS . "catalog" . DS . "product" . $item["file"];
			logger("Unlink: " . $fp);
			print "unlink:" . $fp . "\n";
			unlink($fp);
			$media->remove($prod->getId(), $item["file"]);
		}
	}
	catch(Exception $e){ 
		logger($e->getMessage()); 
	}
	$prod->save();
	
	$name = str_replace("/", "", $name);
	print "id: " . $prod->getId() . "\n"; 
	$visibility = array("image", "small_image", "thumbnail");
	$label = "the1st";
	$target = Mage::getBaseDir("media") . DS . "import" . DS . $name;
	logger("writeImportedAsset: " . $target);
	print "target: " . $target . "\n";
	try{
		$prod->addImageToMediaGallery($target, $visibility, false, false);
		logger("image imported: " . $target);
		print "imported: " . $target . "\n";
	}
	catch(Exception $e){
		logger("could not import image: " . $target);
		logger($e->getMessage());
		print "exception: " . $e->getMessage() . "\n";
		continue;
	}
	$gall = $prod->getData("media_gallery");
	$temp = array_pop($gall["images"]);
	$temp["label"] = $label;
	array_push($gall["images"], $temp);
	$prod->setData("media_gallery", $gall);
	foreach($prod->getMediaGalleryImages() as $image){
		// var_dump($image->debug);
		if(is_file($image->getFile())){
			$path = (string)Mage::helper("catalog/image")->init($prod, "thumbnail", $image->getFile())->keepFrame(false)->resize(640);
			print "path: " . $path . "\n";
		}
	}
	$prod->save();	
}

logger("Done: mygassi-import-assets"); 

exit(1);
