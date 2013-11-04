<?php
require_once("mygassi-config.php");
require_once(mageroot);

Mage::app();




$product = Mage::getModel("catalog/product")->loadByAttribute("sku", "007");
$ref = serialize($product);
print_r($ref);
file_put_contents("/Users/vico/Workspace/ProdImport/data/mprod.php", $ref);
exit(1);









exit(1);
$invoice = Mage::getModel("sales/order_invoice")->loadByIncrementId("100000171");
// print_r($invoice->debug());
// $sale = Mage::getModel("sales/order")->load($invoice->getOrderId());
// print_r ($sale->debug());
// $email = $sale->getCustomerEmail();
// print_r($email);
// $email = $invoice->getCustomerEmail();
// print_r($email);
// $sale->setCustomerEmail("vberzsin@gmail.com");
// $email = $sale->getCustomerEmail();
// print_r($email);
$invoice->sendEmail(); 

exit(1);

$sale = Mage::getModel("sales/order")->loadByIncrementId("1200000020316");

/*
$billing = $sale->getBillingAddress()->getData();
$shipping = $sale->getShippingAddress()->getData();
print_r($billing);
print_r($shipping);
*/

$billing = $sale->getBillingAddress();
$shipping = $sale->getShippingAddress();

/*
print_r($billing->debug());
print_r($shipping->debug());
*/

$lb = "\n";

print $billing->getTelephone();
print $lb;
print $billing->getFax();
print $lb;
print $shipping->getTelephone();
print $lb;
print $shipping->getFax();
print $lb;

exit(1);

/*
print "discount_description:   " . $sale->getDiscountDescription()  . "\n";
print "base_discount_amount:   " . $sale->getBaseDiscountAmount()   . "\n";
print "base_discount_invoiced: " . $sale->getBaseDiscountInvoiced() . "\n";
*/

// $invoice = Mage::getModel("sales/order_invoice")->loadByIncrementId("100000167");
// $invoice = Mage::getModel("sales/order_invoice")->loadByIncrementId("100000166");

// print_r($invoice->debug());

