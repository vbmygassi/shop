<?php
/**
	no way to "test" or "get that run" without the merchant payment test gateway
	through third party redirects and such
 **/

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

class Mygassi_Premium_IndexController extends Mage_Checkout_Controller_Action
{
	// http://192.168.178.27/magento/premium/
	// http://192.168.178.27/magento/premium/index/
	
	var $checkout = null;

	public function indexAction()
	{
		// Mage::getModel("premium/pmodel")->test();

		print Mage::getSingleton("core/layout")
			->createBlock("core/template")
			->setTemplate("premium/form.phtml")
			->toHtml();
		
		return true;
	}

	// http://192.168.178.27/magento/premium/index/login
	public function loginAction()
	{
		// welcome to the amazon: the amazon loves you
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
		
		Mage::getSingleton("core/session")->setErrorMessage("");
		$loc = Mage::getBaseUrl() . "premium";

		// inits cart	
		$cart = Mage::getSingleton("checkout/cart");
		$cart->init();
		
		// empties cart
		if(count($cart->getItems()) > 0){
			$res = Mage::getSingleton('checkout/session')->getQuote()->removeAllItems();
		}
	
		// todo: add the selected premium product	
		// fills cart
		try{
			$prod = Mage::getModel("catalog/product")->loadByAttribute("sku", "01840");
			$cart->addProduct($prod, array("product"=>"01840", "qty"=>"2"));
		}
		catch(Exception $e){
			Mage::getSingleton("core/session")->setErrorMessage($e->getMessage());
			$this->getResponse()->setHeader("Location", $loc)->sendHeaders();
			exit(1);
		}
		
		// saves cart
		$cart->save();
		
		// sets up checkout	
		$this->checkout = Mage::getSingleton('checkout/type_onepage');
		$this->checkout->initCheckout();
		$this->checkout->saveCheckoutMethod("customer");
		// $this->checkout->saveCheckoutMethod("guest");
		
		// sets up billing data
		// billing is kind of a company address
		$p = Mage::getSingleton("core/session")->getCustomerInput();
		$q = Mage::getSingleton("core/session")->getPaymentInput();
		// 	
		
		$d = array();	
		$this->checkout->saveBilling($d, false);
		$this->checkout->saveShipping($d, false);
		
		// sets up shipping method
		$this->checkout->saveShippingMethod("freeshipping_freeshipping", false);
		
		// sets up payment method and cocolores	
		$d = array();
		$d["method"] = $q["payment_type"];
		$d["payone_wallet_type"] = "PPE";
		$d["payone_config_payment_method_id"] = "3";
		$this->checkout->savePayment($d, false);
	
		/*
		Mage/app/code/community/Payone/Core/Helper/Url.php
		The redirect url as for the "success" of  premium user payment is "patched" here: 
		Mage/app/code/core/Mage/Checkout/controllers/OnepageController.php
		(this url is to be submitted to the payment gateway 
		FOR THAT NO ONE CAN REDIRECT A SUCCESS MESSAGE
		*/
	
		// save diss order baby
		// do not save now : still testing
		$res = $this->checkout->saveOrder();
		
		// evaluates redirect url
		// that is the url the gateway (dep. configuration) is about to return
		// sandbox.paypal?with_an_id	
            	$redirectUrl = $this->checkout->getCheckout()->getRedirectUrl(); 
		if("checkmo" == $q["payment_type"]){
			$redirectUrl = Mage::getBaseUrl() . "premium/index/thanks";
		}		

		// redirects withour redirect url	
		switch($redirectUrl){
			case null:
			case "":
				Mage::getSingleton("core/session")->setErrorMessage("Gateway nicht erreicht");
				$this->getResponse()->setHeader("Location", $loc)->sendHeaders();
				exit(1);
		}	
		
		// redirect babäh; don't stop until the fat lady sings
		// payone -> redirects to the payment
		// do not redirect for now
		$this->getResponse()->setHeader("Location", $redirectUrl)->sendHeaders();
	
		return true;
	}

	public function thanksAction()
	{
		/////////////////////////////////////////////////	
		$this->redirectWithoutSession();	
		/////////////////////////////////////////////////	
		
		// resets fck error message
		Mage::getSingleton("core/session")->setErrorMessage("");
		
		print Mage::getSingleton("core/layout")
			->createBlock("core/template")
			->setTemplate("premium/thanks.phtml")
			->toHtml();
		
		return true;
	}

	// diss is the payment type form submit action
	public function ptfsAction()
	{
		/////////////////////////////////////////////////	
		$this->redirectWithoutSession();	
		/////////////////////////////////////////////////	
		
		// resets fck error message
		Mage::getSingleton("core/session")->setErrorMessage("");
		
		// reads post values	
		$post = $this->getRequest()->getPost();

		// no post	
		if(null == $post){
			$loc = Mage::getBaseUrl() . "premium";
			$this->getResponse()->setHeader("Location", $loc)->sendHeaders();
			exit(1);
		}	
		
		Mage::getSingleton("core/session")->setPaymentInput($post);
		
		// copies savement type to the session vars
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
		
		// resets fck error message
		Mage::getSingleton("core/session")->setErrorMessage("");
		
		// renders "check your order" page
		print Mage::getSingleton("core/layout")
			->createBlock("core/template")
			->setTemplate("premium/verify_order.phtml")
			->toHtml();
	
		return true;
	} 

	// that is where the initial [ entry ] premium form gets submitted to
	// premium customer form submit
	public function pcfsAction()
	{
		// resets error message
		Mage::getSingleton("core/session")->setErrorMessage("");
		
		// sets redirect url	
		$loc = Mage::getBaseUrl() . "premium";
		
		// reads post values
		$post = $this->getRequest()->getPost();
		
		// reposts values 
		Mage::getSingleton("core/session")->setCustomerInput($post);
		
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
			Mage::getSingleton("core/session")->setErrorMessage("Bitte &uuml;berpr&uuml;fen Sie Ihre Eingaben");
			$this->getResponse()->setHeader("Location", $loc)->sendHeaders();
			exit(1);
		}
	
		// validates incoming post values
		// name consists of 8 chars
		if(8 > strlen($post["p_name"])){
			Mage::getSingleton("core/session")->setErrorMessage("Bitte geben Sie einen Namen an");
			$this->getResponse()->setHeader("Location", $loc)->sendHeaders();
			exit(1);
		}
		else {
			$post["p_name"] = trim(substr($post["p_name"], 0, 64));
			$temp = explode(" ", $post["p_name"]);
			if(count($temp) >=2){
				$post["p_firstname"] = trim(array_shift($temp));
				$post["p_lastname"] = trim(implode(" ", $temp));
			}
			else {
				$post["p_firstname"] = trim($post["p_name"]);
				$post["p_lastname"] = "";
			} 
		}	
	
		// email validation
		if(!filter_var($post["p_email"])){
			Mage::getSingleton("core/session")->setErrorMessage("Bitte pr&uuml;fen Sie die angegebene Emailadresse");
			$this->getResponse()->setHeader("Location", $loc)->sendHeaders();
			exit(1);
		}
		else {
			$post["p_email"] = trim(substr($post["p_email"], 0, 64));
		}

		// p_telephone
		if(false){
			Mage::getSingleton("core/session")->setErrorMessage("Bitte &uuml;berpr&uuml;fen Sie die Telephonnummer");
			$this->getResponse()->setHeader("Location", $loc)->sendHeaders();
			exit(1);
		}
		else{
			$post["p_telephone"] = trim(substr($post["p_telephone"], 0, 64));
		}
		
		// p_street
		if(8 > strlen($post["p_street"])){
			Mage::getSingleton("core/session")->setErrorMessage("Bitte &uuml;berpr&uuml;fen Sie die Adresse");
			$this->getResponse()->setHeader("Location", $loc)->sendHeaders();
			exit(1);
		}
		else{
			$tmp = explode(" ", $post["p_street"]);
			if(count($tmp) >= 2){
				$post["p_street_1"] = trim(substr(array_shift($tmp), 0, 64));
				$post["p_street_2"] = trim(substr(join(" ", $tmp), 0, 64));
			}
			else{
				$post["p_street_1"] = trim(substr($post["p_street"], 0, 64));
			}
		}
			
		// p_zip
		if(4 > strlen($post["p_zip"])){
			Mage::getSingleton("core/session")->setErrorMessage("Bitte &uuml;berpr&uuml;fen Sie die Postleitzahl");
			$this->getResponse()->setHeader("Location", $loc)->sendHeaders();
			exit(1);
		}
		else{
			$post["p_zip"] = trim(substr($post["p_zip"], 0, 64));
		}

		// ************* ::::::::::::::: ---------------------- *************** 
		// c_street
		if(8 > strlen($post["c_street"])){
			Mage::getSingleton("core/session")->setErrorMessage("Bitte &uuml;berpr&uuml;fen Sie die Adresse");
			$this->getResponse()->setHeader("Location", $loc)->sendHeaders();
			exit(1);
		}
		else{
			$tmp = explode(" ", $post["c_street"]);
			if(count($tmp) >= 2){
				$post["c_street_1"] = trim(substr(array_shift($tmp), 0, 64));
				$post["c_street_2"] = trim(substr(join(" ", $tmp), 0, 64));
			}
			else{
				$post["c_street_1"] = trim(substr($post["c_street"], 0, 64));
			}
		}
			
		// c_zip
		if(4 > strlen($post["c_zip"])){
			Mage::getSingleton("core/session")->setErrorMessage("Bitte &uuml;berpr&uuml;fen Sie die Postleitzahl");
			$this->getResponse()->setHeader("Location", $loc)->sendHeaders();
			exit(1);
		}
		else{
			$post["c_zip"] = trim(substr($post["c_zip"], 0, 64));
		}

		// c_city
		if(4 > strlen($post["c_city"])){
			Mage::getSingleton("core/session")->setErrorMessage("Bitte &uuml;berpr&uuml;fen Sie die Postleitzahl");
			$this->getResponse()->setHeader("Location", $loc)->sendHeaders();
			exit(1);
		}
		else{
			$post["c_city"] = trim(substr($post["c_zip"], 0, 64));
		}

		// c_telephone
		if(false){
			Mage::getSingleton("core/session")->setErrorMessage("Bitte &uuml;berpr&uuml;fen Sie die Telephonnummer");
			$this->getResponse()->setHeader("Location", $loc)->sendHeaders();
			exit(1);
		}
		else{
			$post["c_telephone"] = trim(substr($post["c_telephone"], 0, 64));
		}
		
		// repost customer input
		Mage::getSingleton("core/session")->setCustomerInput($post);

		// inits premium user group
		$this->initPremiumCustomerGroup();

		// reads current customer	
		$customer = Mage::getSingleton("customer/session")->getCustomer();
		
		// inits a new customer
		if(null == $customer){
			$customer = Mage::getModel("customer/customer");
		}

		// sets up customer
		$customer->setFirstname($post["p_firstname"]);
		$customer->setLastname($post["p_lastname"]);
		$customer->setEmail($post["p_email"]);
		$customer->setGroupId($this->premiumGroupId);	
		
		// inits password
		$password = "hokuspokus";
		$customer->setPasswordHash(md5($password));
		// $customer->generatePassword(16);

		// saves a new premium customer	
		try{
			$customer->save();
		}
		catch(Exception $e){
			// "Diese Kunden-Mailadresse existiert bereits"
			// todo: do not load existing user but redirect to the login
			// todo: set some error message into guest session
			$message  = $e->getMessage();
			$message .= '<br/>';
			$message .= '<a href="http://frontend-1722069931.eu-west-1.elb.amazonaws.com/">';
			$message .= '&gt;&gt;&gt Zum Login';
			$message .= '</a>';
			Mage::getSingleton("core/session")->setErrorMessage($message) ;
			$this->getResponse()->setHeader("Location", $loc)->sendHeaders();
			exit(1);
		}
	
		// inits address
		$address = array(
			"customer_id"=>$customer->getId(),
			"firstname"=>$post["p_firstname"],
			"lastname"=>$post["p_lastname"],
			"street"=>array(
				"0"=>$post["p_street_1"],
				"1"=>$post["p_street_2"]
			),
			"city"=>$post["p_city"],
			"region_id"=>$post["p_region_id"],
			"region"=>$post["region"],
			"postcode"=>$post["p_zip"],
			"country_id"=>"DE",
			"telephone"=>$post["p_telephone"],
			"is_default_billing"=>"0",
			"is_default_shipping"=>"0"
		);
		
		// fetches address object
		if($cid = $customer->getDefaultBilling()){
			$ca = Mage::getModel("customer/address")->load($cid);
		} 
		// write new address object
		else {
			$ca = Mage::getModel("customer/address");
		}
			
		// sets up address
		$ca->setData($address)
			->setCustomerId($customer->getId())
			->setSaveInAddressBook("1");

		// saves address
		try{
			$ca->save();
		}	
		catch(Exception $e){
			Mage::getSingleton("core/session")->setErrorMessage($message) ;
			$this->getResponse()->setHeader("Location", $loc)->sendHeaders();
			exit(1);
		}
		
		// inits ze ozer address
		$address = array(
			"customer_id"=>$customer->getId(),
			"firstname"=>$post["p_firstname"],
			"lastname"=>$post["p_lastname"],
			"company"=>$post["c_name"],
			"street"=>array(
				"0"=>$post["c_street_1"],
				"1"=>$post["c_street_2"]
			),
			"city"=>$post["c_city"],
			"region_id"=>$post["c_region_id"],
			"region"=>$post["c_region"],
			"postcode"=>$post["c_zip"],
			"country_id"=>"DE",
			"telephone"=>$post["c_telephone"],
			"is_default_billing"=>"1",
			"is_default_shipping"=>"1"
		);

		// sets up address
		$ca->setData($address)
			->setCustomerId($customer->getId())
			->setSaveInAddressBook("1");

		// saves address
		try{
			$ca->save();
		}	
		catch(Exception $e){
			Mage::getSingleton("core/session")->setErrorMessage($message) ;
			$this->getResponse()->setHeader("Location", $loc)->sendHeaders();
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
			Mage::getSingleton("core/session")->setErrorMessage($e->getMessage());
			$this->getResponse()->setHeader("Location", $loc)->sendHeaders();
			exit(1);
		}
	
		// redirects to "package deal" page	
		$loc = Mage::getBaseUrl() . "premium/index/select_package";
		$this->getResponse()->setHeader("Location", $loc)->sendHeaders();
	
		return true;
	}

	// three step setup of a new premium location
	public function select_packageAction()
	{
		/////////////////////////////////////////////////	
		$this->redirectWithoutSession();	
		/////////////////////////////////////////////////	
	
		// renders "set up a new location" form 
		print Mage::getSingleton("core/layout")
			->createBlock("core/template")
			->setTemplate("premium/package_deal_form.phtml")
			->toHtml();
		
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
		// checks session (customer is authed or not)
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
