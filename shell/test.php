<?php
require_once("mygassi-config.php");
require_once(mageroot);

Mage::app();

$prod = Mage::getModel("catalog/product")->loadByAttribute("sku", "99988877755");
$prod->load($prod->getId());
print_r($prod);
exit(1);

$customer = Mage::getModel("customer/customer")->load("137");
print_r($customer);
print $customer->getId();
exit(1);


$coll = Mage::getModel("customer/entity_address_collection")->setCustomerFilter($customer);
$coll->load();

foreach($coll as $adr){
	print "------------";
	print_r($adr);
	print "<br/>";
}
exit(1);

/*
$customer = Mage::getModel("customer/customer")->load(132);
print_r($customer);
$address = Mage::getModel("customer/address")->load(66);
print_r($address);
i*/


/*
$product = Mage::getModel("catalog/product")->loadByAttribute("sku", "01840");
$product = $product->load($product->getId());
$updated_at = $product->getUpdatedAt();
print_r($updated_at);
$ts = strtotime($updated_at);
$cs = date("U");
print PHP_EOL;
print "ts:" . $ts . PHP_EOL;
print "cs:" . $cs . PHP_EOL;
*/

/**

 Durch sämtliche Produkte hindurch: 
	wenn, wennn wenn, ähh, wenn
		ähh
	prod->getUpdatedAt() 
		grösser ist 
		als erwartet;

	dann alles an den Service (von Vladimir) 
		zurücksenden;

	Whatawowbow
 **/

$coll = Mage::getModel("catalog/product")->getCollection();
foreach($coll as $prod){
	// $prod = $prod->load($prod->getId());
	print "SKU: " . $prod->getSku(). PHP_EOL;
	print "update at: " . strtotime($prod->getUpdatedAt()) . PHP_EOL;
} // somethinglikediss | update my ass | 

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

