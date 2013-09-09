<?php
require_once("mygassi-config.php");
require_once("mygassi-logger.php");
require_once(mageroot);
Mage::app();

logger("Starting: mygassi-categoryimport");

/**
 * sets up a new client
 */
$client = Mage::getModel('codex_api/api');

/** 
 * installs category prouduct-link index
 */ 
// addCategoryAttribute();

/**
 * fetches the "level1" categories 
 */
$target = "category/"; 
if(null !== $response = $client->call($target, 'GET')){
	logger($response);
	$rootCatID = Mage::app()->getStore()->getRootCategoryId();
	logger("rootCatID:" . $rootCatID);
	foreach($response as $key=>$category){
		// $category["path"] = "1/2/" . $category["idCategory"];
		$category["path"] = "1/" .$rootCatID . "/" . $category["idCategory"];
		writeCategory($category);
		getChildCategories($category);
	}
}

/**
 * gets the child categories of a category
 */
function getChildCategories($category)
{
	$id = $category["idCategory"];
	$target = "category/parent_id/" . $id; 
	logger($target);
	$path = $category["path"];
	$client = Mage::getModel('codex_api/api');
	if(null !== ($response = $client->call($target, 'GET'))){
		foreach($response as $key=>$category){
			$category["path"] = $path . "/" . $id;
			writeCategory($category);
			getChildCategories($category);
		}
	}
}

/**
 * writes a category
 * @returns boolean
 */
function writeCategory($category)
{
	$res = false;
	$currentStoreID = Mage::app()->getStore()->getStoreId();
	if(null === ($cat = Mage::getModel("catalog/category")->load($category[idCategory]))){
		logger("new category");
		$cat = Mage::getModel("catalog/category");
	}
	foreach($category['language'] as $languagedata){
		$store_id = getStoreIdByLanguageId($languagedata['idLang']);
		$cat->setStoreId($store_id);
		$extradata = array(
			'headline' => $languagedata['headline'],
			'description' => $languagedata['text']
		);
		foreach($extradata as $key=>$value){ $cat->setData($key, $value); }
		$name = $languagedata['title'];
		$cat->setName($name);
		$cat->setId($category["idCategory"]);
		// fix diss | not sure about the cid clid cpl
		$cpl = $category["cid"];
		if(null === $cpl){ $cpl = $category["clid"]; }
		$cat->setCpl($cpl);
		// -->
		$cat->setErpId($category["idCategory"]);
		$cat->setPosition($category["sortNb"]);
		$cat->setPath($category["path"]);
		$cat->setIsActive(1);
		$cat->setStatus(1);
		$cat->setParentId($category["parent_id"]);
		try{ 
			$cat->save(); 
			$res = true; 
		} 
		catch(Exception $e){ 
			logger("e:" . $e);
			logger("writeCategory::Exception: " . $e->getMessage() . ":" . $store_id); 
			$res = false;
		}
	}
	return $res;
}

function getStoreIdByLanguageId($lid)
{
	logger("getStoreIdByLanguageId: " . $lid);
	$allStores = Mage::app()->getStores();
	$deStoreID = 0;
	$enStoreID = 0;
	foreach ($allStores as $eachStoreId => $val) {
		$storeCode = Mage::app()->getStore($eachStoreId)->getCode();
		$storeName = Mage::app()->getStore($eachStoreId)->getName();
		$storeId = Mage::app()->getStore($eachStoreId)->getId();
		if("default" === $storeCode){ $deStoreID = $storeId; }
		if("de" === $storeCode){ $deStoreID = $storeId; }
		if("en" === $storeCode){ $enStoreID = $storeId; }
	}
	
	switch($lid){
		case "1":
		case "5": 
		case "de":
			logger("ret:" . $deStoreID);
			return $deStoreID;
			break;
		case "2": 
		case "6":
		case "en":
			logger("ret:" . $enStoreID);
			return $enStoreID;
			break;        
	}
        return false;
}

/*
function addCategoryAttribute()
{
	$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
	$res = $setup->addAttribute('catalog_category', 'cpl', array(
		'group'			=> 'Display Settings',
		'label'			=> 'Category-Product-Link',
		'type'			=> 'varchar',
		'input'			=> 'text',
    		'visible'		=> true,
		'required' 		=> false,
    		'user_defined' 		=> true,
		'visible_on_front' 	=> false,
		'unique' 		=> true,
		'sort_order' 		=> 500
	));
	logger("addCategoryAttribute():" . $res);
}
*/

logger("Done: mygassi-categoryimport");
exit(1);
