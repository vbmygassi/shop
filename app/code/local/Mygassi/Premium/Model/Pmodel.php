<?php
class Mygassi_Premium_Model_Pmodel extends Mage_Core_Model_Abstract
{
	protected function _construct()
	{
		$this->_init("premium/pmodel");
	}

	public function loadPremiumProducts()
	{
		$res = array();
		$res[]= Mage::getModel("catalog/product")->loadByAttribute("sku", "99988877755");
		$res[]= Mage::getModel("catalog/product")->loadByAttribute("sku", "99988877756");
		$res[]= Mage::getModel("catalog/product")->loadByAttribute("sku", "99988877757");
		return $res;
	}	
}

