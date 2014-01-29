<?php
class Mygassi_Premium_Model_Pmodel extends Mage_Core_Model_Abstract
{
	protected function _construct()
	{
		$this->_init("premium/pmodel");
	}

	public function test()
	{
		print "Mygassi_Premium_Model_Pmodel::test()";
	}
}
