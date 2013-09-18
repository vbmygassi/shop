<?php
require_once("mygassi-config.php");
require_once(mageroot);

Mage::app();

$invoice = Mage::getModel("sales/order_invoice")->loadByIncrementId("100000165");

$invoice->sendEmail(); 
