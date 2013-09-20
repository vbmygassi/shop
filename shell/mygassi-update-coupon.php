<?php
/**************
 * 
 * }}}
 * Updates the "next" coupon (5 Euros) of a given user
 */


require_once("mygassi-config.php");
require_once(mageroot);

// fetches a new mage instance
Mage::app();

// updates coupon of a given user
function updateCoupon($id)
{
	if(null === $id){ return false; }
	
	$customer = Mage::getModel("customer/customer")->load($id);
	
	// fetches customer		
	$customer = $customer->load($customer->getId());
	
	// fetches code of customer
	$code     = $customer->getInvoiceCoupon();
	$coupon   = Mage::getModel("salesrule/coupon")->load($code, "code"); 
	$used     = $coupon->getTimesUsed();
	$name     = $customer->getName();

	// fetches the name of the customer
	// no coupons for guests ?? guess not	
	switch($name){
		case null;
		case "";
			return false;
	}
	
	// continues without a name of a customer (customer is a guest)
	if(5 > strlen($name)){
		return false;
	}

	// checks whether or not the coupon code is used 	
	if(4 > strlen($code) || 0 < $used){
		// print "writing new code: " . $code . "\n";
		// generates new coupon code (once the coupon code is used)
		$code = generateNextCoupon();
		$customer->setInvoiceCoupon($code);
		$customer->save();
	} 
		
	print "name: " . $name . "\n";
	print "code: " . $code . "\n";
	print "used: " . $used . "\n"; 
}

function generateNextCoupon()
{
	$code = "no-coupon";
	$tries = 1;
	while($tries--){
		$code = Mage::helper("core")->getRandomString(5);
		$res = writeCoupon($code);
		if($res){ $tries = 0; }	
	}	
	return $code;
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
			->setCustomerGroupIds(array(0, 1))
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

$id = $argv[1];

switch($id){
	case null:
	case "":
		print "Usage: mygassi-upgrade-coupon.php [123 userid]\n";
		print "Usage: mygassi-upgrade-coupon.php [all]\n";
		exit(1);	
		break;
	
}

updateCoupon($id);

exit(1);
