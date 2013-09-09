<?php

/*************************
 * }}}
 * Deletes canceled orders
 */

require_once("mygassi-config.php");
require_once(mageroot);

Mage::app('admin')->setUseSessionInUrl(false);                                                                                                                 

$sales = Mage::getModel("sales/order")->getCollection();

foreach($sales as $sale){switch($sale->getCustomerEmail()){
	case "vberzsin@gmail.com":
	case "vberzsin666@gmail.com":
	case "vberzsin777@gmail.com":
	case "vberzsin888@gmail.com":
	case "vberzsin@yahoo.com":
	case "anfaenger@gmail.com":
	case "vb@mygassi.com":
	case "vberzsin@abd1111111.com":
	case "asdfadfs@asdfads.de":
	case "sjsjs@dhshs.de":
		try{
			$sale->load($sale->getIncrementId());
			$sale->delete();
		} 
		catch(Exception $e){
			print $e->getMessage();
			print "\n";
		}
		break;
	}
} 


foreach($sales as $sale){ switch($sale->getIncrementId()){
	case "1200000020210":
	case "1200000020239":
	case "1200000020235":
	case "1200000020230":
	case "1200000020229":
	case "1200000020226":
	case "1200000020224":
	case "1200000020214":
	case "1200000020214":
	case "1200000020213":
	case "1200000020212":
	case "1200000020211":
	case "1200000020210":
	case "1200000020208":
	case "1200000020122":
	case "1200000020123":
	case "1200000020123":
	case "1200000020124":
	case "1200000020125":
	case "1200000020140":
	case "1200000020147":
	case "1200000020148":
	case "1200000020198":
	case "1200000020198":
	case "1200000020207":
	case "1200000020215":
	case "1200000020225":
			try{
			$sale->load($sale->getIncrementId());
			print_r($sale->getIncrementId());
			print "\n";
			$sale->delete();
		} 
		catch(Exception $e){
			print $e->getMessage();
			print "\n";
		}
		break;
	}

}

exit(1);
