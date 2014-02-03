<?php
class Mygassi_Mini_IndexController extends Mage_Checkout_Controller_Action
{
	public function indexAction()
	{
		$coll = Mage::getModel("mini/model")->loadProducts();
		print_r($coll);
		return true;
	}
}

?>
