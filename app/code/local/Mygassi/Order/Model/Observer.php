<?php
/**
 */
class Mygassi_Order_Model_Observer
{
	/**
	 * removes customer from "beginners"
	 */
	public function notifyOrderIsPlaced($observer)
	{
		// print "Mygassi_Order_Model_Observer::notifyOrderIsPlaced()";
		// $this->generateNextCoupon();
	}

	protected function generateNextCoupon()
	{
		$coupon = "no-coupon";
		$tries = 10;
		while($tries--){
			$coupon = Mage::helper("core")->getRandomString(6);
			$res = generateCoupon($coupon);
			if($res){ $tries = 0; }	
		}	
		// print "coupon: " . $coupon . "\n";	
		$id = Mage::getSingleton("customer/session")->getCustomer()->getId();
		$customer = Mage::getModel("customer/customer")->load($id);
		// $customer->setInvoiceCoupon($couponCode);
		// $customer->save();
	}

	protected function writeCopon($code)
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
		catch(Exception $e){ $res = false; }
		return $res;
	}

	/*
	public function notifyAfterMerge($observer)
	{
		print "MyGassi_Order_Model_Observer::notifyAtLogin";
		// this is the items in the "current" : not authed cart
		foreach(Mage::getSingleton("checkout/session")->getQuote()->getItemsCollection() as $fuck){
			var_dump($fuck->debug());
		}
		$userId = Mage::getSingleton("customer/session")->getCustomer()->getId();
		print "userId: " . $userId . "<br>";
		$customerCart = Mage::getModel('checkout/cart')->loadByCustomer($idUser);
		print "customerCart: " . $customerCart . "<br>";
		exit(1);
	}
	*/

	/*
	private function getGroupIdOf($name)
	{
		$claz = new Mage_Customer_Model_Group();
		$coll = $claz->getCollection();
		foreach($coll as $group){
			$code = $group->getCustomerGroupCode();
			$id = $group->getCustomerGroupId();
			switch($code){
				case "General":
					$res = $id;
					break;
				case "" . $name:
					$res = $id;
					continue;
			}	
		}
		return $res;
	}
	*/



	// http://www.blog.magepsycho.com/clear-abandoned-cart-items-during-login-in-magento
	public function clearAbandonedCarts(Varien_Event_Observer $observer)
	{
		$lastQuoteId = Mage::getSingleton('checkout/session')->getQuoteId();
		if($lastQuoteId) {
			$customerQuote = Mage::getModel('sales/quote')->loadByCustomer(Mage::getSingleton('customer/session')->getCustomerId());
			$customerQuote->setQuoteId($lastQuoteId);
			$this->_removeAllItems($customerQuote);
		}
		else {
			$quote = Mage::getModel('checkout/session')->getQuote();
			$this->_removeAllItems($quote);
		}
	}
	
		
	protected function _removeAllItems($quote)
	{
		foreach ($quote->getAllItems() as $item){
			$item->isDeleted(true);
			if ($item->getHasChildren()) {
				foreach ($item->getChildren() as $child) {
					$child->isDeleted(true);
				}
			}
		}
		$quote->collectTotals()->save();
	}
	// http://www.blog.magepsycho.com/clear-abandoned-cart-items-during-login-in-magento

}
