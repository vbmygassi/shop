<?php
/***
 * }
 * }
 * statt dessen sind da "drei" listen voller iregendetwas muss man nicht verstehen
 */
require_once("mygassi-config.php");
require_once("mygassi-logger.php");
require_once(mageroot); 

Mage::app();

/**
 * fetches the products
 */
function fetchNextProduct($args)
{
	// checks whether limit of products to be imported is reached
	if($args["offset"] >= $args["max"]){ finalizeImport(); }
	// fetches the next product
	$target = "article/mygassionly/1/limit/".$args["bulk"]."/offset/".$args["offset"]; 
	logger($target);
	// print $target . "\n";
	if(null === ($client = Mage::getModel('codex_api/api'))){ return; }
	if(null !== ($response = $client->call($target, 'GET'))){
		$args["offset"] += $args["bulk"];
		// writes the imported assets into media catalog/product/..
		// after the product import is done
		if(0 == (count($response))){ finalizeImport(); }
		// writes one product at each cycle
		if(1 == $args["bulk"]){
			writeProduct($response);
			fetchNextProduct($args);
		}
		// writes (read(n)) products (read>1)
		$len = count($response);
		for($i = 0; $i < count($response); $i +=$len){
			foreach($response as $product){
				writeProduct($product);
			}
		}
		fetchNextProduct($args);
	}
}

/**
 *
 */
function finalizeImport()
{
	global $settings; 
	if($settings["import"]){ 
		writeImportedAssets();
	}
	if($settings["reindex"]){ 
		reindexProductCatalog();
	}
	logger("Done: mygassi-productimport");
	exit(1);
}

/**
 * fix diss
 */
function getStoreId($index)
{
	$res = 0;
	try{ $res = Mage::app()->getStore($index)->getId(); }
	catch(Exception $e){ 
		$res = 0; logger("Some exception while getStoreId(): " . $index); 
	}
	logger("storeID: " . $res);
	return $res;
}

/**
 * fetches product attributes from "hokus-pokus-service-2" 
 * with the ids 319, 318, 317
 */
function fetchProductAttributes($sku)
{
	$target = ProductAttributesURL . $sku;
	logger($target);
	$ch = curl_init($target);
	curl_setopt($ch, CURLOPT_URL, $target);
	curl_setopt($ch, CURLOPT_POST, 0);
	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$res = json_decode(curl_exec($ch)); 
	curl_close($ch);
	return $res;
}




/**
 * writes a product
 *
 * needs a refac*
 * imports changed a lot
 * at the and it is 3 !! arrays a product each 
 * and "merkmal_id"s within 
 */
$magicImportIDs = array();
function writeProduct($product)
{
	// print_r($product);
	// return;

	/*
	print '"';
	print $product[0]["sku"];
	print '"';
	
	print ",";
	
	$res = "";
	$res = str_replace('"', "", $product[0]["name"]);
	$res = str_replace(",", " ", $res);
	$res = str_replace('"', '', $res);
	
	print '"';
	print $res;
	print '"';
	
	$res = "";
	foreach($product as $fields){
		if(isset($fields["merkmal_122"])){
			$res = $fields["merkmal_122"];
		}
	}
	// print $product[10]["merkmal_122"];
	
	print ",";
	
	print '"';
	print $res;
	print '"';
	
	print ",";
	
	print '"';
	$res = 0;
	$res = $product[0]["price"];
	// $res = Mage::helper('core')->currency($res, true, false);
	// $res = str_replace(",", ".", $res);
	// $res = str_replace(".", ",", $res);
	print $res;
	print '"';
	
	$res = str_replace(".", ",", $res);
	print ",";
	print '"';
	print $res; 
	print '"';
		
	print "\n";
	return; 
	*/

	global $settings;	
	global $magicImportIDs;
	global $productContainer;
	logger("writeProduct()");
	// returns with the "old" export format
	if(3 == count($product)){
		logger("Deprecated product import format; returning");
		return;
	}
	// ** selects largest product id
	$sql = "select max(entity_id) from catalog_product_entity";
	$res = Mage::getSingleton('core/resource')->getConnection("core_read")->fetchAll($sql);
	$pid = (int)$res[0]["max(entity_id)"];
	$pid += 1;
	logger("pid:" . $pid);;

	$sku = $product[0]["sku"];
	logger("sku:" . $sku);

	// returns without the sku	
	if(null === $sku){ 
		logger("No sku found; returning");
		return; 
	}
	
	// loads product by sku
	$coll = Mage::getModel("catalog/product")->getCollection();
	$coll->addFieldToFilter("sku", $sku);
	$prod = null;
	foreach($coll as $prod){ 
		$prod = $prod->load($prod->getId()); 
		$pid = $prod->getId();
		logger("Product found:" . $prod->getSku()); 
	};

	// reads a product from store 	
	if(null === $prod){
		logger("New product"); 
		$coll = Mage::getModel("catalog/product")->getCollection();
		foreach($coll as $p){ 
			// selects a product
			$prod = $p->load($p->getId());
			logger("Reading first prod from collection");
			break;
		}
	}
		
	// reads product from persisted product instance
	if(null === $prod){
		logger("Using serialized prod");
		// selects serialized product
		global  $pmod; 
		$prod = $pmod;
	} 

	// sets attribute id	
	$prod->setAttributeID(Mage::getModel("eav/entity_attribute_set")
		->getCollection()
		->setEntityTypeFilter(Mage::getModel("eav/entity")->setType("catalog_product")->getTypeId())
		->addFieldToFilter("attribute_set_name", "Default")
		->getFirstItem()
		->getAttributeSetId());
	
	// extracts linked categories	
	$lc = array();
	for($i = 0; $i < count($product); $i++){
		$cat = $product[$i]["_category"];
		if(null === $cat){ continue; }
		$temp = explode(".", $cat);
		$cat = $temp[1];
		if("" !== $cat){ $lc[] = $cat; }
	}
	$cats = implode(",", $lc);
	logger("Cats: " . $cats);
	$prod->setCategoryIds($cats);

	if($settings["attrdownload"]){
		$productAttributes = fetchProductAttributes($sku);
	}
	
	$additional = array();
	foreach($productAttributes->attributes as $key=>$value){
		switch($key){
			case "318":
				foreach($value as $kkey=>$vvalue){
					$loc = "en";
					if("1" === $kkey){ 
						$loc = "de"; 
					}
					foreach($vvalue as $kkkey=>$vvvalue){
						if("value" === $kkkey){
							$additional[$loc][0] = $vvvalue;
						}
					}
				}
				break;
			case "319":
				foreach($value as $kkey=>$vvalue){
					$loc = "en";
					if("1" === $kkey){ $loc = "de"; }
					foreach($vvalue as $kkkey=>$vvvalue){
						if("value" === $kkkey){
							$additional[$loc][1] = $vvvalue;
						}
					}
				}
				break;
			case "320":
				foreach($value as $kkey=>$vvalue){
					$loc = "en";
					if("1" === $kkey){ $loc = "de"; }
					foreach($vvalue as $kkkey=>$vvvalue){
						if("value" === $kkkey){
							$additional[$loc][2] = $vvvalue;
						}
					}
				}
				break;
		}
	}

	// extracts product image | codex !!	
	$productImagePath = null; 
	foreach($product as $p){ 
		if(isset($p["merkmal_473"])){ 
			$productImagePath = $p["merkmal_473"]; 
		} 
	}
	
	// assume there is no "merkmal_473"
	switch($productImagePath){
		case null:
		case "":
			$productImagePath = $product[0]["image"];
	}

	// print ">>" . $productImagePath . "\n";

	// extracts category image | codex 
	$categoryImagePath = null;
	foreach($product as $p){ 
		if(isset($p["merkmal_280"])){ 
			$categoryImagePath = $p["merkmal_280"]; 
		}
	}

	/*
	$cid = $lc[0];
	if(null !== ($cat = Mage::getModel("catalog/category")->setStoreId("default")->load($cid))){
		$cat->setStoreId(getStoreId("default"));
		$cat->setData("add_desc_1", $additional["de"][0]);
		$cat->setData("add_desc_2", $additional["de"][1]);
		$cat->setData("add_desc_3", $additional["de"][2]);
		print "" . $cat->getId() . ":"  . $cat->getName() . "\n";
		$cat->save();
	}
	*/

	/*
	if(null !== ($cat = Mage::getModel("catalog/category")->setStoreId(getStoreId("en"))->load($cid))){
		$cat->setStoreId(getStoreId("default"));
		$cat->setData("add_desc_1", $additional["en"][0]);
		$cat->setData("add_desc_2", $additional["en"][1]);
		$cat->setData("add_desc_3", $additional["en"][2]);
		$cat->save();
	}
	*/

	// adds a product image
	/*
	fetchImage($categoryImagePath);
	$target = Mage::getBaseDir("media") . DS . "import" . DS . $categoryImagePath;

	$data['display_mode'] = 'PRODUCTS_AND_PAGE';
	$data['page_layout'] = 'one_column';
	$data['thumbnail'] = $target;
	$cat->addData($data);              
	$cat->save();
	*/
	// print "sku: " . $sku . "\n";
	// exit(1);
	
	// $cat->addImageToMediaGallery($target, array("thumbnail"), true, false);
	// $cat->setDisplayMode("PRODUCTS_AND_PAGE");
	// $cat->setPageLayout("one_column");
	// $cat->saveThumbnail($target); 
	// $cat->save();
	// this might fail with two ore more parent categories	
		
	// sets website ids
	$prod->setWebsiteIds(array(1));
	
	// deletes values
	$prod->setMetaTitle("");
	$prod->setMetaDescription("");
	$prod->setMetaKeyword("");
	$prod->setUrlKey("");
	$prod->setManufacturer("");
	
	// gets the index of global values in an ever changing import array 
	for($i = 0; $i < count($product); $i++){
		if(isset($product[$i]["_product_websites"])){
			if("base" === $product[$i]["_product_websites"]){
				$index = $i;
				$magicImportIDs["global"] = $index;
				break;
			}
		}
	}
	// sets global store values
	$prod->setId($pid);
	$prod->setSku($sku);
	$prod->setName(UTF8($product[$index]["name"]));
	$prod->setGlobalName(UTF8($product[$index]["global_name"]));
	$prod->setTypeId($product[$index]["_type"]);
	$prod->setStatus(1);
	$prod->setDescription(UTF8($product[$index]["description"]));
	$prod->setShortDescription(UTF8($product[$index]["short_description"]));
	$prod->setPrice($product[$index]["price"]);
	$prod->setTaxClassId($product[$index]["tax_class_id"]);
	$prod->setIsInStock($product[$index]["is_in_stock"]);
	$prod->setUseConfigManageStock($product[$index]["use_config_manage_stock"]);
	$prod->setVisibility($product[$index]["visibility"]);
	$prod->setQty($product[$index]["qty"]);
	$prod->setBasisartikelnr($product[$index]["basisartikelnr"]);

	/*
	$prod->setImage($product[$index]["image"]);
	$prod->setThumbnail($product[$index]["thumbnail"]);
	$prod->setSmallImage($product[$index]["small_image"]);
	$prod->setMediaImage($product[$index]["_media_image"]);
	$prod->setMediaPosition($product[$index]["_media_position"]);
	if($settings["fetch"]){ 
		fetchImage($product[$index]["image"]); 
	}
	*/

	$prod->setSmallImage($productImagePath);
	$prod->setThumbnail($productImagePath);
	$prod->setImage($productImagePath);
	$prod->setMediaImage($productImagePath);
	if($settings["fetch"]){ 
		fetchImage($productImagePath); 
	}

	// additional fields
	// get stored in the categores....
	// 
	$prod->setArtikelbezeichnung1($additional["de"][0]);
	$prod->setArtikelbezeichnung2($additional["de"][1]);
	$prod->setArtikelbezeichnung3($additional["de"][2]);
	// karlie related product attributes
	// karlie related product attributes
	/*
	$prod->setBasisArtikelNr($product[$index]["basisartikelnr"]);
	$prod->setPackagingUnit($product[$index]["packaging_unit"]);
	$prod->setLength($product[$index]["length"]);
	$prod->setWidth($product[$index]["width"]);
	$prod->setHeight($product[$index]["height"]);
	$prod->setDiameter($product[$index]["diameter"]);
	$prod->setVolume($product[$index]["volume"]);
	$prod->setSize($product[$index]["size"]);
	$prod->setBargain($product[$index]["bargain"]);
	$prod->setNew($product[$index]["new"]);
	$prod->setWeight($product[$index]["weight"]);
	$prod->setTierart($product[$index]["tierart"]);
	$prod->setColor($product[$index]["color"]);
	*/
	// 
	$prod->save(); // saves default view 

	// sets magic index of the magic import interface
	// de is "one" (i guess)
	$index = 1;
	$magicImportIDs["de"] = $index;
	
	// sets "de" (default) store values
	$prod->setStoreId(getStoreId("default"));
	$prod->setName(UTF8($product[$index]["name"]));
	$prod->setDescription(UTF8($product[$index]["description"]));
	$prod->setShortDescription(UTF8($product[$index]["short_description"]));

	/*	
	$prod->setMediaImage($product[$index]["_media_image"]);
	$prod->setThumbnail($product[$index]["thumbnail"]);
	$prod->setSmallImage($product[$index]["small_image"]);
	$prod->setImage($product[$index]["image"]);
	$prod->setMediaPosition($product[$index]["_media_position"]);
	if($settings["fetch"]){ fetchImage($product[$index]["image"]); }
	*/

	// saves "de" store view
	$prod->save();  

	// gets the index of "en" store values in an ever changing import array 
	for($i = 0; $i < count($product); $i++){
		if(isset($product[$i]["_store"])){
			if("en" === $product[$i]["_store"]){
				$index = $i;
				$magicImportIDs["en"] = $index;
				break;
			}
		}
	}
	
	// sets "en" store values
	$prod->setStoreId(getStoreId("en"));
	$prod->setName(UTF8($product[$index]["name"]));
	$prod->setDescription(UTF8($product[$index]["description"]));
	$prod->setShortDescription(UTF8($product[$index]["short_description"]));

	/*
	$prod->setArtikelbezeichnung1($additional["en"][0]);
	$prod->setArtikelbezeichnung2($additional["en"][1]);
	$prod->setArtikelbezeichnung3($additional["en"][2]);
	*/

	/*
	$prod->setMediaImage($product[$index]["_media_image"]);
	$prod->setThumbnail($product[$index]["thumbnail"]);
	$prod->setSmallImage($product[$index]["small_image"]);
	$prod->setImage($product[$index]["image"]);
	$prod->setMediaPosition($product[$index]["_media_position"]);
	if($settings["fetch"]){ fetchImage($product[$index]["image"]); }
	*/
	// saves "en" store view
	$prod->save(); 

	// finds and fetches images whithin the magic arry
	/*
	$characteristica = array(
			"_media_image", 
			"thumbnail", 
			"small_image", 
			"image", 
			"_media_attribute_id",
			"_media_is_disabled",
			"_media_position"
	);
	for($i = 0; $i < count($product); $i++){
		$probablyAnImageAsset = 0;
		for($ii = 0; $ii < count($characteristica); $ii++){
			if(isset($product[$i][$characteristica[$ii]])){
				$probablyAnImageAsset += 1;	
			}
		}
		if(7 == $probablyAnImageAsset){
			$magicImportIDs["images"][$sku][] = $i; 
			logger("Magic image found at " . $i . " : " . $product[$i]["image"]);
			if($settings["fetch"]){ 
				fetchImage($product[$i]["image"]); 
			}
		}
	}
	*/

	$productContainer[]= $product;
	logger("Written:" . $prod->getSku()); 
	return true;
}


/**
 * empties the gallery of one product 
 */
function clearGallery($prod)
{
	logger("clearGallery()");
	// removes existing media gallery
	// http://stackoverflow.com/questions/5709496/magento-programmatically-remove-product-images
	try{
		if($prod->getId()){
			$media = Mage::getModel("catalog/product_attribute_media_api");
			$items = $media->items($prod->getId());
			foreach($items as $item){
				$fp = Mage::getBaseDir("media") . DS . "catalog" . DS . "product" . $item["file"];
				logger("Unlink: " . $fp);
				unlink($fp);
				$media->remove($prod->getId(), $item["file"]);
			}
		}
	}
	catch(Exception $e){ logger($e->getMessage()); }
}

/**
 * rebuilds the db indexes
 */
function reindexProductCatalog()
{
	/*
	$proc = Mage::getModel('index/process')->load(6);
	$proc->reindexAll();
	*/
	for($i = 1; $i <= 9; $i++){
		$proc = Mage::getModel("index/process")->load($i);
		$proc->reindexAll();
	}
}

/**
 * writes imported assets to the magento cache machinery
 * there is two assets defined within the imported object
 * i would not knwo the reason
 * they get labeled the1st and the2nd from now on
 * image paths delivered by the import objects look like: "magpic/[some.png]"
 * the "magpic/" is *wrong
 */
function writeImportedAssets()
{
	logger("writeImportedAssets()");
	global $productContainer;
	global $magicImportIDs;
	foreach($productContainer as $product){
		if(false === ($prod = Mage::getModel("catalog/product")->loadByAttribute("sku", $product[0]["sku"]))){ 
			return false; 
		};
		// clears gallery of current product
		clearGallery($prod);
		// imports the "default" image
		$prod = $prod->load($prod->getId());
		$prod->setStoreId(getStoreId("default"));
		$visibility = array("image", "small_image", "thumbnail");
		$name = str_replace("magpic/", "", $prod->getImage());
		// print ">" . $prod->getImage() . "\n";
		writeImportedAsset($prod, $name, "the1st", $visibility);
		// imports magic images
		/*
		$sku = $product[0]["sku"];
		$importedFileNames = array();
		$magicIndex = 0;
		for($i = 0; $i < count($magicImportIDs["images"][$sku]); $i++){
			$id = $magicImportIDs["images"][$sku][$i];
			// do not import the same magic asset twice
			$fileName = $product[$id]["image"];
			if(!in_array($fileName, $importedFileNames)){
				$importedFileNames[]= $fileName;
				$label = "magic-asset-" . $magicIndex;
				$magicIndex++;
				$visibility = array();
				writeImportedAsset($prod, $name = str_replace("magpic/", "", $fileName), $label, $visibility);
			}
		}
		*/
		$prod->save();
	}
	return true;
}

/**
 * write one imported asset to the magento catalog
 * with a given label
 */
function writeImportedAsset($prod, $name, $label, $visibility)		
{
	if("" === $name){ return false; }
	$target = Mage::getBaseDir("media") . DS . "import" . DS . $name;
	logger("writeImportedAsset: " . $target);
	try{
		$prod->addImageToMediaGallery($target, $visibility, false, false);
		logger("image imported: " . $target);
	}
	catch(Exception $e){
		logger("could not import image: " . $target);
		logger($e->getMessage());
	}
	$gall = $prod->getData("media_gallery");
	$temp = array_pop($gall["images"]);
	$temp["label"] = $label;
	array_push($gall["images"], $temp);
	$prod->setData("media_gallery", $gall);
	foreach($prod->getMediaGalleryImages() as $image){
		Mage::helper("catalog/image")->init($prod, "thumbnail", $image->getFile())->keepFrame(false)->resize(640);
	}	
	return true;
}

/** 
 * fetches an image asset from host each
 */
function fetchImage($name)
{ 
	if(null === $name){ return; }
	$name = str_replace("magpic/", "", $name);
	$target = MygassiImportImagePath . $name;
	$dest = Mage::getBaseDir("media") . DS . "import" . DS . $name;
	logger("fetching: " . $target);
	logger("to:       " . $dest);
	$fp = fopen($dest, "w");
	$ch = curl_init($target);
	curl_setopt($ch, CURLOPT_TIMEOUT, 120);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_FILE, $fp);
	$res = curl_exec($ch); 
	$res = curl_close($ch);
	fclose($fp);
	return $res;
}

/**
 * the big nada  
 */
function UTF8($string)
{
	// do not do a thing :: strings are already utf8
	// $string = utf8_encode($string);
	$string = removeGarbageCharacters($string);
	return $string;
}

/**
 * remove garbage from the import
 * the strings are utf8 but garbage filled
 */
function removeGarbageCharacters($string)
{	
	$temp = $string;
	$temp = str_replace("Ã¼", "ü", $temp);
	$temp = str_replace("Ã¤", "ä", $temp);
	$temp = str_replace("Ã¶", "ö", $temp);
	$temp = str_replace("Ã", "ß", $temp);
	$temp = str_replace("Ã", "ß", $temp);
	$temp = str_replace("", "", $temp);
	$temp = str_replace("Â°", "°", $temp);
	return $temp;
}

/***
 * init : reads product model
 * 	: writes a product container for loc recs
 *	: adds a store view "en"
 * 	: adds product attributes
 *	: fetches next product 
 */

logger("Starting: mygassi-productimport");
/***
 * sqldump before import
 */
// require_once(SQLDumpPath);

/***
 * the product model
 */
$pmod = unserialize(file_get_contents(ProductModelPath, true));


/***
 * copy of products : (assetimport; log)
 */
$productContainer = array();

/***
 * adds the store view "en"
 * (store view is added but still it fails for some reason
 * (admin->system->stores->add-store-view->en->English
 */ 
// addStoreView();

/***
 * adds the product attributes
 * mygassi
 * is stream product
 * is top product
 */
// addProductAttribute();

/****
 * import settings
 */
$settings = array(
	"offset" 	=> 0, 
	"bulk" 		=> 1, 
	"max" 		=> 500, 
	"fetch" 	=> true, 
	"import" 	=> true,
	"attrdownload"	=> false,
	"reindex" 	=> true 
);
/***
 * fetches the next product
 * 
 */
fetchNextProduct($settings);
