<?php
require_once("mygassi-config.php");
require_once(mageroot);

$client = new Zend_XmlRpc_Client('https://shop.mygassi.com/api/xmlrpc');
$session = $client->call('login', array('Schnuggywuggy', '2317.schnuggy'));
$result = $client->call('call', array($session, 'catalog_product.list'));
print_r($result);

exit(1);
