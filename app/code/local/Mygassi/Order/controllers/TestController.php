<?php
/*******************************************************************************************

	this controller parses the posted JSON representation 
	of a shopping cart which is submitted by the mobile app
	mobile app client needs a cookie first
	formate!! fixdiss

********************************************************************************************/
class Mygassi_Order_TestController extends Mage_Core_Controller_Front_Action
{
	public function indexAction()
	{
		$target = Mage::getBaseUrl() . "/order/";
print <<<EOD
<html><head><title></title>
<style type="text/css">
textarea{ width:350mm; height:90mm; font-size:4mm; line-height:6.5mm;};
</style>
</head>
<body>
<form method="post" action="{$target}">
<textarea name="cart">
{
	"products":[
		{ "sku":"25118", "qty":"1" },
		{ "sku":"01842", "qty":"2" }
	]
}
</textarea>
<br/>
<input type="submit" name="submit"/>
</form>
</body>
</html>

EOD;

		$quote = Mage::getSingleton('checkout/session')->getQuote();
		$cartItems = $quote->getAllVisibleItems();
		foreach ($cartItems as $item) {
    			$item = $item->load($item->getId());
			print ":" . $item->getSku();
		}
	}
}
