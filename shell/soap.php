<?php

$proxy = new SoapClient("https://shop.mygassi.com/api/v2_soap?wsdl=1");
$proxy->startSession();
$id = $proxy->login("Schnuggywuggy", "2317.schnuggy");
$result = $proxy->catalogCategoryTree($id, 1);
 print_r($result);

 exit(1);
