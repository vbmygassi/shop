<?php
require_once("mygassi-config.php");
require_once(mageroot);
Mage::app();

$coll = Mage::getModel("salesrule/rule")->getCollection();
foreach($coll as $rule){
	print $rule->getName() . "\n";
	if("5,- EUR für die nächste Bestellung" === $rule->getName()){
		$rule->delete();
	}
}
exit(1);

$coll = Mage::getModel("customer/customer")->getCollection();

foreach($coll as $customer){
	$customer = $customer->load($customer->getId());
	print $customer->getInvoiceCoupon() . "\n";
	print $customer->getName() . "\n";
	switch($customer->getName()){
		case null;
		case "";
			continue;
	}
	switch($customer->getInvoiceCoupon()){
		case null:
		case "":
			$code = generateNextCoupon();
			print "code: $code\n";
			$customer->setInvoiceCoupon($code);
			$customer->save();
	}
}

function generateNextCoupon()
{
	$coupon = "no-coupon";
	$tries = 1;
	while($tries--){
		$coupon = Mage::helper("core")->getRandomString(5);
		$res = writeCoupon($coupon);
		if($res){ $tries = 0; }	
	}	
	return $coupon;
}

function writeCoupon($code)
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

exit(1);
