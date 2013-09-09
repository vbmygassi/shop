<?php
/*******************************************************************************************

	this controller parses the posted JSON representation 
	of a shopping cart which is submitted by the mobile app
	mobile app client needs a cookie first
	http://10.14.10.20/Git/magento-project-karlie/webservice/public/index.php/v1/article/id/4711/mg_stockd

 }}}}
 ****/

class Mygassi_Order_IndexController extends Mage_Core_Controller_Front_Action
{
	const NO_CART_SUBMITTED   = 'No cart submitted.';
	const NO_SUCH_PRODUCT_SKU = 'No such product.';
	const NO_PRODUCT_ADDED    = 'No product added.';
	const INVALID_CART_JSON   = 'Invalid cart json.';
	const PRODUCTS_ADDED      = 'Products added to cart.';
	const ERROR_CODE          = '307';
	const SUCCESS_CODE        = '1';

	private function notify($code, $message)
	{
		print json_encode(array('resultCode'=>$code, 'resultMessage'=>$message));
	}

	public function indexAction()
	{
		// fetches the cart request
		$req = $this->getRequest()->getParam("cart");
		$req = rawurldecode($req);	
		
		// returns if there is no request	
		switch($req){
			case null:
			case "":
			$this->notify(self::ERROR_CODE, self::NO_CART_SUBMITTED);
			return;
		}
		
		// decodes the submitted JSON : returns if the json is invalid
		$jcart = null;
		try{
			$jcart = json_decode($req);
		}
		catch(Exception $e){
			print $e->getMessage();
			exit();
		}
		if(null == $jcart){
			$this->notify(self::ERROR_CODE, self::INVALID_CART_JSON);
			return;
		}
	
		// fetches the cart
		$mcart = Mage::getSingleton("checkout/cart");
		// inits the cart
		$mcart->init();
	
		// empties the cart (before the new cart load)
		// again a special wonsch
		//
		// --> not sure whether this breaks the session
		// it'd need a redirect
		// i'd suggest a "remove" button in the mobile app but than who cares...
		// ..
		// 	
		// oh my god, this is not happening to me; there is something in my cart!
		// oh my god, what are we going to do now? 	
		// viktor what are we gonna do now?
		// what we gonna do for money viktor?
		//
		//
		// 
		// 
		// viktor?
		//
		//
		//
		if(count($mcart->getItems()) > 0){
			Mage::getSingleton('checkout/session')->getQuote()->removeAllItems();
		}

		// places each product in to the cart
		foreach($jcart->products as $index=>$product){
			try{
				// selects product
				$coll = Mage::getModel("catalog/product")->getCollection();
				$sku = null;
				$sku = $product->sku; if(null === $sku){ $sku = $product->id; }
				$coll->addFieldToFilter("sku", $sku);
				$prod = null;
				foreach($coll as $prod){ $prod = $prod->load($prod->getId()); }	
				// notifies about missing product
				if(null === $prod){ 
					$this->notify(self::ERROR_CODE, self::NO_SUCH_PRODUCT_SKU . " " . $sku); 
					return;
				}
				// adds product to cart
				$mcart->addProduct($prod, array("product"=>$sku, "qty"=>$product->qty));
			}
			catch(Mage_Core_Exception $e){ 
				$this->notify(self::ERROR_CODE, $e->getMessage()); 
			}
		}
		
		// persists the cart
		$mcart->save();
		
		// redirects to the checkout one page
		$this->_redirect("checkout/onepage");
		/*
		$redirect = "checkout/onepage"; 
		$promos = Mage::getModel("salesrule/rule")->getCollection();
		if(count($promos) > 0){ $redirect = "checkout/cart"; }
		$this->_redirect($redirect); 
		*/
	}
}

?>
