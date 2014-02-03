<?php
class Mygassi_Mini_Model_Model extends Mage_Core_Model_Abstract
{
	protected function _construct()
	{
		$this->_init("mini/model");
	}

	public function loadProducts()
	{
		$res = array();
		$res[]= Mage::getModel("catalog/product")->loadByAttribute("sku", "99988877755");
		return $res;
	}	
}

