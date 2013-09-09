<?php
/*******************************************************************************************

	this controller parses the posted JSON representation 
	of a shopping cart which is submitted by the mobile app
	mobile app client needs a cookie first
	formate!! fixdiss

********************************************************************************************/
class Mygassi_Order_CaptureController extends Mage_Core_Controller_Front_Action
{
	public function indexAction()
	{
		print "Mygassi_Order_CaptureController::indexAction()\n";
		$capture = serialize(Mage::app()->getRequest()->getPost());
		print $capture;
		file_put_contents("capture.out", $capture);
	}
}
