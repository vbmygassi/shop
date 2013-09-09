<?php 
/*** 
	JSON Export Service
	prints out the product catalog as json
	.. > todo: put it in the model to acces it with the cron job
 **/
class Mygassi_Jsonexport_IndexController extends Mage_Core_Controller_Front_Action
{
	public function indexAction()
	{ 
		$catalog = Mage::getBaseDir("media") . DS . "json" . DS . "productcatalog.json";
		$chunksize = 1 *(1024);  
		$buffer = ""; 
		$handle = fopen($catalog, "r"); 
		if(false === $handle) { return false; }
		while (!feof($handle)) {
			print fread($handle, $chunksize);
			ob_flush(); 
			flush(); 
		}
		fclose($handle);
	}
}
?>
