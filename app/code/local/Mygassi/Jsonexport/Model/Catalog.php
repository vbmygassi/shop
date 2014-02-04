<?php
class Mygassi_Jsonexport_Model_Catalog extends Mage_Core_Model_Abstract
{
	protected function _construct()
	{
		$this->_init("jsonexport/catalog");
	}

	public function writeProductCatalog()
	{
		$json = json_encode($this->getProducts());
		$path = Mage::getBaseDir("media") . DS . "json" . DS . "productcatalog.json";
		$fp = fopen($path, "w");
		fwrite($fp, $json);
		fclose($fp);
	}
	
	private function getCategories()
	{
		$res = array();
		$categories = Mage::getModel("catalog/category")->getCollection();
		// $categories = Mage::getModel("catalog/category")->getCollection()->addAttributeToSelect("active", 1);
		foreach($categories as $cat){
			$cat = Mage::getModel("catalog/category")->load($cat->getId());
			$res[]= array(
				'id'=>$cat->getId(),
				'title'=>rawurlencode($cat->getName())
			);
		}
		return $res;
	}

	private function getProducts()
	{
		$mprds = array();
		$products = Mage::getModel("catalog/product")->getCollection();
		foreach($products as $product){
			// continues with deactivated product 
			$active = Mage::getModel("catalog/product_status")->getProductStatus(array($product->getId()));
			$deactivated = false; 
			// "$active[<somenum>] = 2" means product is deactivated
			// :))
			foreach($active as $entry){ if(2 == $entry){
				$deactivated = true; 
			}}	
			if($deactivated){ continue; }
			// fetches product
			$prod = Mage::getModel("catalog/product")->setStoreId($this->getStoreId("default"))->load($product->getId());
			//-->			
			// fix diss : images??? which ones???
			$imageURL1st = "";
			foreach($prod->getMediaGalleryImages() as $image){
				$path = Mage::helper("catalog/image")->init($prod, "thumbnail", $image->getFile())->keepFrame(false)->resize(640);
				if("the1st" === $image->getLabel()){ 
					$imageURL1st = (string)$path; 
				} 
			}	
			switch($imageURL1st){
				case "":
				case null:
					$path = Mage::helper("catalog/image")->init($prod, "thumbnail", "default.png")->keepFrame(false)->resize(640);
					$imageURL1st = (string)$path;
		
			}
			//-->			
			// specialwonsch : whatawonsch
			// category IDs get one category ID
			// $catID = array_shift($prod->getCategoryIds());
			$catIDs = $prod->getCategoryIds();
			$catID = $catIDs[1];
			$cat = Mage::getModel("catalog/category")->load($catID);
			// category image
			$catImage = "";
			$path = Mage::helper("catalog/image")->init($prod, "thumbnail", $cat->getImage())->keepFrame(false)->resize(640);
			$catImage = (string)$path;
			// created.. 
			$created = strtotime($prod->getCreatedAt());
			if($this->getProductTop($prod)){ 
				$created = date("U"); 
			}
			if($this->getProductStream($prod)){ 
				$created = date("U"); 
			}
			// specialwonsh : the description is NOT the description! 
			// description is the new detail
			// desc is the 2
			/*
			$description = $prod->getArtikelbezeichnung2();
			switch($description){
				case null:
				case "":
					// but than, desc is sometimes the 3
					$description = $prod->getArtikelbezeichnung3();
					break;
			}
			switch($description){
				case null:
				case "":
					// if there is no description so far
					$descTemp1 = $prod->getArtikelbezeichnung1();
					$descTemp2 = $prod->getDescription();
					// description is the new description
					$description = $descTemp2; 
					// except of there is a "more" characters in the group-detail
					if(strlen($descTemp1) > strlen($descTemp2)){
						// group detail is the new description
						$description = $descTemp1;
					}
			}
			*/
			// fills json array 
			$temp = array(
				'category_id'=>$cat->getId(),
				'category_path'=>$cat->getPath(),
				'category_name'=>$cat->getName(),
				'category_description'=>$cat->getDescription(),
				'category_image'=>$catImage,
				'sku'=>$prod->getSku(),
				'id'=>$prod->getSku(),
				'title'=>$prod->getName(),
				'old_price'=>$this->getPhoneFormattedPrice($prod->getOldPrice()),
				'price'=>$this->getPhoneFormattedPrice($prod->getPrice()),
				'image_url'=>$imageURL1st,
				'description'=>$prod->getDescription(),
				'short_description'=>$prod->getDescription(),
				'created'=>$created,
				'basisartikelnr'=>$prod->getBasisartikelnr(),
				'is_top_product'=>$this->getProductTop($prod),
				'is_stream_product'=>$this->getProductStream($prod)
			);
			$mprds[]= $temp; 
			// specialwonsch again
			// products tagged as "top_product" generate a new category and get collected again!
			if("1" === $this->getProductTop($prod)){
				$temp = array(
					'category_id'=>"$66666666666666",
					'category_path'=>$cat->getPath(),
					'category_name'=>"Top-Produkte",
					'category_description'=>$cat->getDescription(),
					'category_image'=>$catImage,
					'sku'=>$prod->getSku(),
					'id'=>$prod->getSku(),
					'title'=>$prod->getName(),
					'old_price'=>$this->getPhoneFormattedPrice($prod->getOldPrice()),
					'price'=>$this->getPhoneFormattedPrice($prod->getPrice()),
					'image_url'=>$imageURL1st,
					'description'=>$prod->getDescription(),
					'short_description'=>$prod->getDescription(),
					'created'=>$created,
					'basisartikelnr'=>$prod->getBasisartikelnr(),
					'is_top_product'=>$this->getProductTop($prod),
					'is_stream_product'=>$this->getProductStream($prod)
				);
				$mprds[]= $temp; 
			}
		}
		return $mprds;
	}
	

	private function getStoreId($code="default")
	{
		$allStores = Mage::app()->getStores();
		$res = 0;
		foreach ($allStores as $eachStoreId => $val) {
			$storeCode = Mage::app()->getStore($eachStoreId)->getCode();
			$storeId = Mage::app()->getStore($eachStoreId)->getId();
			if($code === $storeCode){ $res = $storeId; }
		}
		return $res;
	}
	
	private function getProductStream($prod)
	{
		$res = $prod->getIsStreamProduct();
		if(null === $res){ $res = 0; }
		return $res;
	}
	
	private function getProductTop($prod)
	{
		$res = $prod->getIsTopProduct();
		if(null === $res){ $res = 0; }
		return $res;
	}

	private function getPhoneFormattedPrice($price)
	{	
		$price = (float)$price;
		$price = round($price, 2);
		$price *= 100;
		return $price;
	}
}
