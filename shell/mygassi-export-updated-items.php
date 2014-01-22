<?php
require_once("mygassi-config.php");
require_once("mygassi-logger.php");
require_once(mageroot);

Mage::app();

logger("starting mygassi-export-updated-items");

// assume last import was yesterday
$last_import_timestamp = date("U") -(60 *60 *24);

// loops through products | ?? categories 
// evaluates the updated items 
$coll = Mage::getModel("catalog/product")->getCollection();
foreach($coll as $prod){
	$prod_updated_timestamp = strtotime($prod->getUpdatedAt());
	// $prod_created_timestamp = strtotime($prod->getCreatedAt());
	$prod_created_timestamp = $last_import_timestamp;
	/*
	print "updated: " . $prod_updated_timestamp . PHP_EOL;
	print "created: " . $prod_created_timestamp . PHP_EOL;
	print "---" . PHP_EOL;
	*/
	if($prod_created_timestamp < $prod_updated_timestamp){
		// posts updated product to karlie 
		print "updated: " . $prod_updated_timestamp . PHP_EOL;
		print "created: " . $prod_created_timestamp . PHP_EOL;
		print "---" . PHP_EOL;
		post_updated_product($prod);
	}
} 

// writes updated items 
// --> service machina vladimir gerdt
// --> @karlie
function post_updated_product($prod)
{
	// loads product
	$prod = $prod->load($prod->getId());
	
	// evaluates image path
	$image_path = "";
	foreach($prod->getMediaGalleryImages() as $image){
		if("the1stImage" === $image->getLabel()){ 
			// $image_path = Mage::helper("catalog/image")->init($prod, "thumbnail", $image->getFile())->keepFrame(false)->resize(640);
			$image_path = $image->getPath();
		}
	}

	// fills post arguments	
	$postargs = array
	(
		'sku' => $prod->getSku(),
		'mygassi_headline' => $prod->getName(),
		'mygassi_text' => $prod->getDescription(),
		'mygassi_uvp' => $prod->getOldPrice(),
		'mygassi_image' => '@' . "'" . $image_path . "'"
	);
	// $postargs = json_encode($postargs);
	print_r($postargs);
	print PHP_EOL;
	logger("submitting updated item (sku): " . $prod->getSku());

// ----------------------------------------------------------------------------------------------
/*
 * Author: Vladimir Gerdt
 * Company: Karlie Heimtierbedarf GmbH
 * Date: 21st Oktober 2013
 * Update: 08th November 2013
 * Version: 1.1
 * Description:
 *      POST: update one or more attributes for an item by sku.
 * Parameters:
 *      sku: article number ( string ).
 *      mygassi_headline: ( string ), if empty then there is no changes.
 *      mygassi_text: ( string ), if empty then there is no changes.
 *      mygassi_uvp: ( decimal ), if empty then there is no changes.
 *      mygassi_image: @path/to/image ( jpg, png, git, tif ), if empty then there is no changes.
 */
// $request =  'http://cs.karlie.de/karlie/index.php?forward=webservice/mygassi/article.php';
$request =  'http://10.14.10.37/karlie/index.php?forward=webservice/mygassi/post_article.php';

// postargs get prfilled above -->vberzsin@mygassi.com 
/*
$postargs = array
(
    'sku' => '02270',
    'mygassi_headline' => "Rondo Halsbänder mit Halstuch",
    'mygassi_text' => "Genietet.\nMit Zierreifel.\nVerchromte Beschläge.",
    'mygassi_uvp' => 8.95,
    'mygassi_image' => '@' . '/var/www/CS12/karlie/webservice/mygassi/image/Tulips.jpg'
);
*/
// 02260_MG_01.tif

$headers = array(
    'Accept: application/json',
    'Content-Type: application/json'
);

$handle = curl_init();

curl_setopt($handle, CURLOPT_URL, $request);
curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);

curl_setopt($handle, CURLOPT_POST, true);
curl_setopt($handle, CURLOPT_POSTFIELDS, $postargs);

$response = curl_exec($handle);
$code = curl_getinfo($handle, CURLINFO_HTTP_CODE);

// close cURL resource, and free up system resources
curl_close($handle);
// output
// ----------------------------------------------------------------------------------------------

	logger("post of item (sku): " . $prod->getSku() . " done with code: " . $code . " response: " . $response);
	print "response: " . $response . PHP_EOL; 
}

logger("mygassi-export-updated-items done");

exit(1);
