<?php
/*******************************************************************************************
 *
 *	Handkurbel für die Cron Jobs
 *	die irgendwann Cron Jobs sind und hoffentlich nicht im Webroot arbeiten
 *
 *******************************************************************************************/
class Mygassi_Status_IndexController extends Mage_Core_Controller_Front_Action
{
	public function indexAction()
	{
		print "Mygassi_Status_IndexController::indexAction()";	
	}

	// /var/www/mygassi.com/htdocs/cron.php (der ret ist in den "config.xml")
	/*
	public function testPayoneCronAction()
	{
		print "Mygassi_Status_IndexController::testPayoneCronAction()\n";	
		require_once("/home/vberzsin/shell3/mygassi-payone-cron.php");
	}
	*/

	public function testSalesExportAction()
	{
		print "Mygassi_Status_IndexController::testSalesExportAction()\n";	
		require_once("/home/vberzsin/shell3/mygassi-salesexport.php");
	}

	public function testCheckParcelsAction()
	{
		print "Mygassi_Status_IndexController::testCheckParcelsAction()\n";	
		require_once("/home/vberzsin/shell3/mygassi-check-parcels.php");
	}
	
	public function testRetoureAction()
	{
		print "Mygassi_Status_IndexController::testRetoureAction()\n";	
		require_once("/home/vberzsin/shell3/mygassi-retoure.php");
	}
	
	public function testExportProductCatalogAction()
	{
		// print "Mygassi_Status_IndexController::testExportProductCatalogAction()\n";	
		// cycle through products
		// init images with 640px
		print "http://mygassi.invaliddomain.de/media/json/productcatalog.json";	
		require_once("/home/vberzsin/shell3/mygassi-export-productcatalog.php");
	}
}
?>
