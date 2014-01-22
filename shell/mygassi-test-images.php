<?php
require_once("mygassi-config.php");
require_once("mygassi-logger.php");
require_once(mageroot);

Mage::app();
logger("Starting: mygassi-test-images");

$buff = <<<EOD
<html>
	<head>
		<title></title>
		<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
	</head>
	<body>
EOD;




$coll = Mage::getModel("catalog/product")->getCollection();
$index = 0;
foreach($coll as $prod){
	$prod = $prod->load($prod->getId());
	
	
	$cids = $prod->getCategoryIds();
	$catNames = array();
	foreach($cids as $id){
		$cat = Mage::getModel("catalog/category")->load($id);
		$cat = $cat->load($cat->getId());
		$catNames[]= $cat->getName();
	}

	$sku = $prod->getSku();
	$title = $prod->getName();
	$description = $prod->getDescription();
	$groupDetail = $prod->getArtikelbezeichnung1();
	$detail = $prod->getArtikelbezeichnung2();
	$description = $prod->getDescription();
	$price = Mage::helper("core")->currency($prod->getPrice());
	$top = $prod->getIsTopProduct();
	$stream = $prod->getIsStreamProduct();
	$buff .= <<<EOD
	<hr/>
	<br>
	<b>SKU:</b>
	<br/>{$sku}
	<br/><b>Inkrement:</b> 
	<br/>{$index}
	<br/><b>Titel:</b>
	<br>{$title}
	<br/><b>Preis:</b>
	<br>{$price}
	<br/><b>Beschreibung:</b>
	<br/>{$description}
	<br/><b>Detail:</b> 
	<br/>{$detail}
	<br/><b>Gruppendetail:</b>
	<br/>{$groupDetail}
	<br/><b>Top:</b>
	<br/>{$top}
	<br/><b>Stream:</b>
	<br/>{$stream}
<br/>
EOD;

	$buff .= "<br/><b>Kategorien</b>";
	foreach($catNames as $catName){
		$buff .= "<br/>" . $catName;
	}
	

	foreach($prod->getMediaGalleryImages() as $image){
		$path = (string)Mage::helper("catalog/image")->init($prod, "thumbnail", $image->getFile())->keepFrame(false)->resize(640);
		$filename = $image->getFile();
		$filename = $image->getFile();
		$temp = explode(".", $filename);
		$filename = $temp[0];
		$suff = $temp[1];
		$filename = explode("/", $filename);
		$filename = array_pop($filename);
		$filename = explode("_", $filename);
		$filename = $filename[0];
		$filename = $filename . "." . $suff;
		$imgfile  = trim($sku) . "." . trim($filename); 
		$buff .= <<<EOD

	<br/><b>Bild:</b> 
	<br>{$imgfile}
	<br/><b>Bild: Datei:</b> 
	<br/>{$filename}
	<br/><a href="{$path}">{$path}</a>
	<br/><img src="{$path}" width="250">
	<br/>

EOD;
		$index++;
	}
}





$buff .= <<<EOD
	</body>
</html>

EOD;


file_put_contents(ImageTestPath, $buff);

exit(1);
