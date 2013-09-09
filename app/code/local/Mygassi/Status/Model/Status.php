<?php
class Mygassi_Status_Model_Status extends Mage_Core_Model_Abstract
{
	protected function _construct()
	{
		$this->_init("status/status");
	}
	
	public function exportSales()
	{
		require_once("/home/vberzsin/shell3/mygassi-salesexport.php");
	}

	public function checkParcels()
	{
		require_once("/home/vberzsin/shell3/mygassi-check-parcels.php");
	}

	public function checkRetoure()
	{
		require_once("/home/vberzsin/shell3/mygassi-retoure.php");
	}
}
?>
