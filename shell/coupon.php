<?php
require_once("mygassi-config.php");
require_once(mageroot);
Mage::app();


function generateCoupon(String $code)
{
	try{
		$res = Mage::getModel('salesrule/rule')
			->setName("5,- EUR für die nächste Bestellung")
			->setDescription("5,- EUR für die nächste Bestellung")
			->setCouponCode($code)
			->setUsesPerCoupon(1)
			->setUsesPerCustomer(1)
			->setIsAdvanced(1)
			->setCustomerGroupIds(array(1))
			->setIsActive(1)
			->setCouponType(2)
			->setStopRulesProcessing(0)
			->setSimpleAction('cart_fixed')
			->setDiscountAmount(5)
			->setDiscountQty(null)
			->setDiscountStep('0')
			->setSimpleFreeShipping('0')
			->setApplyToShipping('0')
			->setIsRss(0)
			->setUsesPerCoupon(1)
			->setUsesAutoGeneration(0)
			->setWebsiteIds(array(1))
			->save();
	}
	catch(Exception $e){
		$res = false;	
	}
	return $res;
}

// 
$coupon = "no-coupon";

// a ten tries to generate a new coupon
$tries = 10;
while($tries--){
	$coupon = Mage::helper("core")->getRandomString(6);
	$res = generateCoupon($coupon);
	if($res){ $tries = 0; }	
}	

print "coupon: " . $coupon . "\n";	

$customer = Mage::getModel("customer/customer")->load(13);
$customer->setInvoiceCoupon($couponCode);
$customer->save();

