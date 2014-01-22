<?php
class Mygassi_Jsonexport_Model_Observer
{
	public function test($observer)
	{
		if($observer->getEvent()->getProduct()->hasDataChanges()){
			print "x";
			$sku = $observer->getEvent()->getProduct()->sku;
			$prod = Mage::getModel("catalog/product")->loadByAttribute("sku", $sku);
			$prod->setUpdatedAt(date("U"));
			$prod->save();
		}
		else {
			print "y";
		}
	}
}
