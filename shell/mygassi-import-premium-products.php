<?php
require_once("mygassi-config.php");
require_once(mageroot);

Mage::app();

// BRONZE 
$path = Mage::getBaseDir() . "/media/premium/bronze.obj.php";
$prod = unserialize(file_get_contents($path));
$prod->save();

// SILVER
$path = Mage::getBaseDir() . "/media/premium/silver.obj.php";
$prod = unserialize(file_get_contents($path));
$prod->save();

// GOLD
$path = Mage::getBaseDir() . "/media/premium/gold.obj.php";
$prod = unserialize(file_get_contents($path));
$prod->save();

exit(1);
