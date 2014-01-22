<?php
/***
	a web form 
		for a premium user
		to be filled out
		as for to enable a premium user to consume premium services
			diss script writes a customer of group "premium" into the magento db
			for the payone payment gateway thingy 
				( paypal, debit, whatever... 
				  payone returns the state of the pending payment to this mygassi magento instance
			once the money is transferred
				the premium services (?) kind of get enabled (?) probably 
				at the mygassi node couchbase instance,
				which is located somewhere else: 	
					http://frontend-1722069931.eu-west-1.elb.amazonaws.com/
				it is saideth that
				passwords get generated "there"
			the only reason to store the premium customer is the usage of the payone payment gateway
				[ for that i do not have to store credit cards and such
					for that i do not have to implement the complete payment gateway
						which is implemented in the magento payone api already
					for that i do not have to implement the complete consumer relationship management
						[ invoices, reminders, canceled orders, whatever... 
			but apart from that the implementatino of the premium services takes place (?) at the  mygassi node couchbase instance

	there is no "log-in" in this forms
	nor there shall eveer be such; 
	the login is: 
		http://frontend-1722069931.eu-west-1.elb.amazonaws.com/

	this is strange but... 

	the premium services is magento products
		which get placed into the cart
		and get checked out and payed 
			with the magento payone payment gateway api
		same goes for the tracking of the payment
	|| and then i don't know	
	there is some kind of "sync" going to happen between the mygassi magento and mygassi node [ buziness-stäck ] 
	some json files will sync the premium customer 
 	
	the disguisting things people do no.: 8819871
	total fun conscieous don't cry, dry your eye thingy; 
	here we go: 
 ****/

// class Mygassi_Premium_IndexController extends Mage_Core_Controller_Front_Action
class Mygassi_Premium_IndexController extends Mage_Checkout_Controller_Action
{
	// http://192.168.178.27/magento/premium/
	// http://192.168.178.27/magento/premium/index
	
	var $onePage = null;

	public function indexAction()
	{
		$blck = Mage::getSingleton("core/layout");
		$buff = $blck->createBlock("core/template");
		$buff->setTemplate("premium/form.phtml");
		print $buff->toHtml();
		
		return true;
	}

	// http://192.168.178.27/magento/premium/index/login
	public function loginAction()
	{
		// welcome to amazon: amazon loves you
		$loc = "http://frontend-1722069931.eu-west-1.elb.amazonaws.com/";
		$this->getResponse()->setHeader("Location", $loc)->sendHeaders();
		
		return true;
	}

	// diss where the order is placed
	public function place_orderAction()
	{
		/////////////////////////////////////////////////	
		$this->redirectWithoutSession();	
		/////////////////////////////////////////////////	

		// inits cart	
		$cart = Mage::getSingleton("checkout/cart");
		$cart->init();
		// empties cart
		if(count($cart->getItems()) > 0){
			$res = Mage::getSingleton('checkout/session')->getQuote()->removeAllItems();
		}
		// fills cart
		try{
			$prod = Mage::getModel("catalog/product")->loadByAttribute("sku", "01840");
			$cart->addProduct($prod, array("product"=>"01840", "qty"=>"2"));
		}
		catch(Exception $e){
			print_r($e->getMessage());
		}
		// saves cart
		$cart->save();
			
		// $this->onePage = new Mage_Checkout_Model_Type_Onepage();
		$this->onePage = Mage::getSingleton('checkout/type_onepage');
		$this->onePage->initCheckout();
		$this->onePage->saveCheckoutMethod("guest");
		
		// sets up billing data
		$p = Mage::getSingleton("core/session")->getPostValues();
		// 	
		$d = array();
		$d["firstname"] = $p["p_name"];	
		$d["lastname"] = $p["p_name"];	
		$d["company"] = $p["c_name"];	
		$d["street"][0] = $p["p_street"];	
		$d["street"][1] = $p["p_street"];	
		$d["city"] = $p["p_city"];	
		$d["postcode"] = $p["p_postcode"];	
		$d["telephone"] = $p["p_telephone"];	
		$d["country_id"] = "de";	
		$d["postcode"] = "1234";	
		$d["use_for_shipping"] = "1";
		$this->onePage->saveBilling($d, false);
		
		$d["use_for_shipping"] = "1";
		$d["same_as_billing"] = "1";		
		$this->onePage->saveShipping($d, false);
		
		$this->onePage->saveShippingMethod("freeshipping_freeshipping");
		
		$d = array();
		$d["method"] = $p["payment_type"];
		$d["payone_wallet_type"] = "PPE";
		$d["payone_config_payment_method_id"] = "3";

		// $this->onePage->savePayment($d);
		$this->onePage->savePayment(array("method"=>"checkmo"));
		
		$res = $this->onePage->saveOrder();
		
		$loc = Mage::getBaseUrl() . "premium/index/thanks";
		$this->getResponse()->setHeader("Location", $loc)->sendHeaders();
	}

/*
	public function f(){
		// fixdiss
		// if(null == $this->onePage){ $this->onePage = new Mage_Checkout_Model_Type_Onepage(); }	
		
		// reads post values	
		$post = $this->getRequest()->getPost();
		$accept = $post["accept"];
		
		// redirects without accept
		if("yes_i_do" != $accept){
			$loc = Mage::getBaseUrl() . "premium/index/verify";
			$this->getResponse()->setHeader("Location", $loc)->sendHeaders();
			exit(1);
		}

		// evaluates current customer	
		$customer = Mage::getSingleton("customer/session")->getCustomer();
		
		// todo: add street etc...
		$customer = $customer->loadByEmail($customer->getEmail());
		// todo: do not load daniel
		$customer = $customer->loadByEmail("daniel.zwigart@gmx.de");
	
		// fills up quote	
		// $this->onePage->getQuote()->setCustomer($customer);	
	
		// evaluates address id of customer
		$customerAddressId = $customer->getDefaultBilling();
	
		// sets up billing data
		$p = Mage::getSingleton("core/session")->getPostValues();
	
		// 	
		$d = array();
		$d["firstname"] = $p["p_name"];	
		$d["lastname"] = $p["p_name"];	
		$d["company"] = $p["c_name"];	
		$d["street"][0] = $p["p_street"];	
		$d["street"][1] = $p["p_street"];	
		$d["city"] = $p["p_city"];	
		$d["postcode"] = $p["p_postcode"];	
		$d["telephone"] = $p["p_telephone"];	
		$d["country_id"] = "DE";	
		$d["use_for_shipping"] = "1";
		
		// saves billing related data
		try{
			$res = $this->onePage->saveBilling($d, $customerAddressId);
		}
		catch(Exception $e){
			// todo: redirect with error
			print_r($e->getMessage());
			exit(1);
		}
		
		// evaluates shipping id of customer
		$shippingAddressId = $customer->getDefaultShipping();

		// sets up shipping data 
		// [ we do not need it ]
		$d["use_for_shipping"] = "1";
		$d["same_as_billing"] = "1";		
	
		// saves shipping related data
		try{
			$res = $this->onePage->saveShipping($d, $shippingAddressId);
		}
		catch(Exception $e){
			// todo: redirect with error message
			print_r($e->getMessage());
			exit(1);
		}
		// evaluates shipping method
		// we do not need it 
		// there is no shipping	
		$d = array();
		// $d["shipping_method"] = "freeshipping_freeshipping";
		$d = "freeshipping_freeshipping";	
		
		// saves shipping method	
		try{
			// $res = $this->onePage->getQuote()->getShippingAddress()->setShippingMethod($d);
			// $res = $this->onePage->saveShippingMethod($d);
			// $res = Mage::getModel("customer/address")->load($shippingAddressId);
			// $res = $this->onePage->getQuote()->isVirtual();	
			// print_r($res);
		}
		catch(Exception $e){
			// todo: redirect with error message
			print_r($e->getMessage());
			exit(1);
		}
		
		// collects totals
		// $res = $this->onePage->getQuote()->collectTotals()->save();
		$res = Mage::getSingleton('checkout/session')->getQuote()->collectTotals()->save();

		// sets up payment method data 
		$d = array();
		$d["method"] = $p["payment_type"];
		
		// saves payment method
		try{
			$res = $this->onePage->savePayment($d);
		}
		catch(Exception $e){
			// todo: write error into session : redirect
			print_r($e->getMessage());
			exit(1);
		}
	
		// sets up order data 
		$d = array();
		$d["method"] = $p["payment_type"];
		$d["payone_wallet_type"] = $p["payone_wallet_type"];
		$d["payone_config_payment_method_id"] = $p["payone_config_payment_method_id"];
		
		$d["payone_wallet_type"] = "PPE";
		$d["payone_config_payment_method_id"] = "3";
		
		// diss i do not know
		try{	
			// $res = $this->onePage->getQuote()->getPayment()->importData($d);
			$res = Mage::getSingleton('checkout/session')->getQuote()->getPayment()->importData($d);
		}
		catch(Exception $e){
			// todo: write error into session : redirect with error
			print $e->getMessage();
			exit(1);
		}
	
		// saves quote	
		// $res = $this->onePage->getQuote()->save();
	
		// inits the cart
		$cart = Mage::getSingleton("checkout/cart");
		$cart->init();
	
		// empties cart
		if(count($cart->getItems()) > 0){
			$res = Mage::getSingleton('checkout/session')->getQuote()->removeAllItems();
		}

		// fills cart
		try{
			$prod = Mage::getModel("catalog/product")->loadByAttribute("sku", "01840");
			$cart->addProduct($prod, array("product"=>"01840", "qty"=>"5"));
		}
		catch(Exception $e){
			print_r($e->getMessage());
		}

		// saves cart
		$cart->save();
		
		// saves order
		try{
			$res = $this->onePage->saveOrder();
		}
		catch(Exception $e){
			print "saveOrder()";
			print $e->getMessage();
			exit(1);
		}
		
		print "saveOrder: " . PHP_EOL;
		
		// $loc = Mage::getBaseUrl() . "premium/index/thanks";
		// $this->getResponse()->setHeader("Location", $loc)->sendHeaders();

		return true;	
	}
*/

	public function thanksAction()
	{
		/////////////////////////////////////////////////	
		$this->redirectWithoutSession();	
		/////////////////////////////////////////////////	
		
		$blck = Mage::getSingleton("core/layout");
		$buff = $blck->createBlock("core/template");
		$buff->setTemplate("premium/thanks.phtml");
		print $buff->toHtml();
		
		return true;
	}

	// diss is the payment type form submit action
	public function ptfsAction()
	{
		/////////////////////////////////////////////////	
		$this->redirectWithoutSession();	
		/////////////////////////////////////////////////	
		
		// reads post values	
		$post = $this->getRequest()->getPost();
	
		if(null == $post){
			return false;	
		}	
		
		// evaluates payment type	
		$paymentType = $post["payment_type"];
			
		switch($paymentType){
			case null:
			case "":
				$paymentType = "payone_wallet"; 
				break;
		}	
		
		// copies savement type to the session vars
		// Mage::getSingleton("core/session")->setPaymentType($paymentType);
		$post = Mage::getSingleton("core/session")->getPostValues();
		$post["payment_type"] = $paymentType;
		$res = Mage::getSingleton("core/session")->setPostValues($post);
		// redirects to verify your order page	
		$loc = Mage::getBaseUrl() . "premium/index/verify";
		$this->getResponse()->setHeader("Location", $loc)->sendHeaders();
		
		return true;
	}

	// diss is the verify your order page 
	public function verifyAction()
	{
		/////////////////////////////////////////////////	
		$this->redirectWithoutSession();	
		/////////////////////////////////////////////////	
		
		// renders "check your order" page
		$blck = Mage::getSingleton("core/layout");
		$buff = $blck->createBlock("core/template");
		$buff->setTemplate("premium/verify_order.phtml");
		print $buff->toHtml();
	
		return true;
	} 

	// that is where the initial [ entry ] premium form gets submitted to
	// premium customer form submit
	public function pcfsAction()
	{
		// redirect for something goes wrong	
		$loc = Mage::getBaseUrl() . "premium/index";
		
		// reads post values
		$post = $this->getRequest()->getPost();
		
		// reposts values 
		Mage::getSingleton("core/session")->setPostValues($post);
		
		// redirects without post values
		if(
			!isset($post["p_name"]) ||
			!isset($post["p_email"]) ||
			!isset($post["p_telephone"]) ||
			!isset($post["p_street"]) ||
			!isset($post["p_zip"]) ||
			!isset($post["p_city"]) ||
			!isset($post["c_name"]) ||
			!isset($post["c_street"]) ||
			!isset($post["c_zip"]) ||
			!isset($post["c_city"]) ||
			!isset($post["c_telephone"]) ||
			!isset($post["c_website"])
		){
			$this->getResponse()->setHeader("Location", $loc)->sendHeaders();
			exit(1);
		}
	
		// validates incoming post values
		// name consists of 8 chars
		if(8 > strlen($post["p_name"])){
			$this->getResponse()->setHeader("Location", $loc)->sendHeaders();
			exit(1);
		}
		else {
			$post["p_name"] = substr($post["p_name"], 0, 64);
		}	
	
		// email validation
		if(!filter_var($post["p_email"])){
			$this->getResponse()->setHeader("Location", $loc)->sendHeaders();
			exit(1);
		}
		else {
			$post["p_email"] = substr($post["p_email"], 0, 64);
		}

		// todo: telphone validation
		// todo: ***************************************** to do ::: do that fckng validation fun routines

		// adds premium customer
		// for that they can pay the premium djuty
		// the charge
		// todo: premium customer shall not buy nor login
		// todo: in fact pc's "can" buy: no probs with money
		// 
		$this->initPremiumCustomerGroup();

		// reads current customer	
		$customer = Mage::getSingleton("customer/session")->getCustomer();
		
		// writes a new customer
		if(null == $customer){
			$customer = Mage::getModel("customer/customer");
		}

		$customer->setFirstname($post["p_name"]);
		$customer->setLastname($post["p_name"]);
		$customer->setEmail($post["p_email"]);
		$customer->setGroupId($this->premiumGroupId);	

		// todo: passwords
		$password = "root";
		$customer->setPasswordHash(md5($password));
	
		try{
			$customer->save();
			// todo: init customer session with diss user
		}
		catch(Exception $e){
			// "Diese Kunden-Mailadresse existiert bereits"
			// todo: do not load existing user but redirect to the login
			// todo: set some error message into guest session
			// $this->getResponse()->setHeader("Location", $loc)->sendHeaders();
			print_r($e->getMessage());
			exit(1);
		}
		
		// inits a customer session
		// which might be somezing else than the guest session
		// ....
		try{
			Mage::getSingleton("customer/session")->login($customer->getEmail(), $password);
			Mage::getSingleton("customer/session")->setCustomerAsLoggedIn($customer);
		}
		catch(Exception $e){
			print_r($e->getMessage());
			exit(1);
		}
	
		// redirects to "set up a new location" page	
		$loc = Mage::getBaseUrl() . "premium/index/select_package_deal";
		$this->getResponse()->setHeader("Location", $loc)->sendHeaders();
	
		return true;
	}

	// three step setup of a new premium location
	public function select_package_dealAction()
	{
		/////////////////////////////////////////////////	
		$this->redirectWithoutSession();	
		/////////////////////////////////////////////////	
	
		// renders "set up a new location" form 
		$blck = Mage::getSingleton("core/layout");
		$buff = $blck->createBlock("core/template");
		$buff->setTemplate("premium/package_deal_form.phtml");
		print $buff->toHtml();
		
		return true;
	}

	// inits customer group of type premium
	// **if there is no such
	var $premiumGroupId = -1;
	protected function initPremiumCustomerGroup()	
	{
		// tax class id
		$taxClassId = 3;
			
		// walks through customer groups and selects "Premium"
		foreach(Mage::getModel("customer/group")->getCollection() as $group){
			if("NOT LOGGED IN" === $group->getCustomerGroupCode()){
				$taxClassId = $group->getTaxClassId();
			}
			if("Premium" === $group->getCustomerGroupCode()){
				$this->premiumGroupId = $group->getCustomerGroupId();
			}	
		}
		
		// creates a customer group of type "Premium" with a taxId of all existing groups
		if(-1 == $this->premiumGroupId){
			$this->premiumGroupId = Mage::getModel("customer/group")
				->setCode("Premium")
				->setTaxClassId($taxClassId)
				->save()->getCustomerGroupId();
		}
		
		return true;
	}

	// redirects without a session
	protected function redirectWithoutSession()
	{
		// checks session (custom user is authed or not)
		$customerSession = Mage::getSingleton("customer/session")->isLoggedIn();
		if(!$customerSession){
			$loc = Mage::getBaseUrl() . "premium";
			$this->getResponse()->setHeader("Location", $loc)->sendHeaders();
			exit(1);
		}
		
		// checks whether or not customer is of type "Premium"
		$customerGroupId = Mage::getSingleton("customer/session")->getCustomerGroupId();
		$customerGroupCode = Mage::getSingleton("customer/group")->load($customerGroupId)->getCustomerGroupCode();
		if("Premium" != $customerGroupCode){
			$loc = Mage::getBaseUrl() . "premium";
			$this->getResponse()->setHeader("Location", $loc)->sendHeaders();
			exit(1);
		}
		
		return true;
	}
}

?>
