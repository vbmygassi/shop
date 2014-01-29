<?php
require_once("mygassi-config.php");
require_once(mageroot);

Mage::app();

// bronze
$prod = Mage::getModel("catalog/product")->loadByAttribute("sku", "99988877755");
print_r($prod);

// silver 
$prod = Mage::getModel("catalog/product")->loadByAttribute("sku", "99988877756");
print_r($prod);

// gold 
$prod = Mage::getModel("catalog/product")->loadByAttribute("sku", "99988877757");
print_r($prod);

