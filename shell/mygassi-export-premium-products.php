<?php
require_once("mygassi-config.php");
require_once(mageroot);

Mage::app();

// BRONZE 
$path = Mage::getBaseDir() . "/media/premium/bronze.obj.php";
$prod = Mage::getModel("catalog/product")->loadByAttribute("sku", "99988877755");
file_put_contents($path, serialize($prod));
print $path . PHP_EOL;

// SILVER
$path = Mage::getBaseDir() . "/media/premium/silver.obj.php";
$prod = Mage::getModel("catalog/product")->loadByAttribute("sku", "99988877756");
file_put_contents($path, serialize($prod));
print $path . PHP_EOL;

// GOLD
$path = Mage::getBaseDir() . "/media/premium/gold.obj.php";
$prod = Mage::getModel("catalog/product")->loadByAttribute("sku", "99988877757");
file_put_contents($path, serialize($prod));
print $path . PHP_EOL;

exit(1);
