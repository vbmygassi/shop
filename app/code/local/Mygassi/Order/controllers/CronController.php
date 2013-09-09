<?php
class Mygassi_Order_CronController extends Mage_Core_Controller_Front_Action
{
	public function indexAction()
	{
		print '<a href="cron/jsonexport">mygassi-jsonexport</a>';
		print '<a href="cron/productimport">mygassi-productimport</a>';
		print '<a href="cron/salesexport">mygassi-salesexport</a>';
		print '<a href="cron/parcelid">mygassi-parcelid</a>';
		print '<a href="cron/categoryimport">mygassi-categoryimport</a>';
	}
	
	public function payoneAction()
	{
		$test = new Payone_Core_Model_Factory();
		$service = $test->getServiceTransactionStatusExecute();
		// var_dump($service);
		$count = $service->executePending();
		// var_dump($count);
	}

	public function jsonexportAction()
	{
		$path  = Mage::getBaseDir();
		$path .= DS . "shell" . DS . "mygassi-jsonexport.php"; 
		print "req:" . $path . "<br/>";
		require_once($path);	
	}

	public function productimportAction()
	{
		$path  = Mage::getBaseDir();
		$path .= DS . "shell" . DS . "mygassi-productimport.php"; 
		print "req:" . $path . "<br/>";
		require_once($path);	
	}
	
	public function salesexportAction()
	{
		$path  = Mage::getBaseDir();
		$path .= DS . "shell" . DS . "mygassi-salesexport.php"; 
		print "req:" . $path . "<br/>";
		require_once($path);	
	}
	
	public function parcelidAction()
	{
		$path  = Mage::getBaseDir();
		$path .= DS . "shell" . DS . "mygassi-parcelid.php"; 
		print "req:" . $path . "<br/>";
		require_once($path);	
	}
	
	public function categoryimportAction()
	{
		$path  = Mage::getBaseDir();
		$path .= DS . "shell" . DS . "mygassi-categoryimport.php"; 
		print "req:" . $path . "<br/>";
		require_once($path);	
	}
}
