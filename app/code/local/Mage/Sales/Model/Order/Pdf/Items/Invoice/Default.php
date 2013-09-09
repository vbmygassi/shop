<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Sales Order Invoice Pdf default items renderer
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class Mage_Sales_Model_Order_Pdf_Items_Invoice_Default extends Mage_Sales_Model_Order_Pdf_Items_Abstract
{
	public function draw()
	{
		$order  = $this->getOrder();
		$item   = $this->getItem();
		$pdf    = $this->getPdf();
		$page   = $this->getPage();
		
		$lines  = array();
		$qty  = round($item->getQty());
		$title = Mage::helper('core/string')->str_split($item->getName(), 70, true, true);
		
		if(false !== $prod = Mage::getModel("catalog/product")->loadByAttribute("sku", $this->getSku($item))){
			$prod->load($prod->getId());
			foreach($prod->getMediaGalleryImages() as $image){
				// $file = Mage::getBaseDir("media") . "/catalog/product" . $image->getFile();
				$path = Mage::helper("catalog/image")->init($prod, "thumbnail", $image->getFile())->keepFrame(false)->resize(360);
				$file = (string)$path;
				$file = str_replace(Mage::getBaseUrl(), "/", $file);
				$file = Mage::getBaseDir() . $file;
			}
		}	
      	
		$price = $order->formatPriceTxt($item->getPriceInclTax());
		$total = $order->formatPriceTxt($item->getRowTotal() +$item->getTaxAmount());

// $i = 0;
// for($i = 0; $i < 100; $i++){	
		$lines[$i][] = array('feed'=>35,  'align'=>'right', "font_size"=>7, "height"=>50, "text"=>$qty);
		$lines[$i][] = array('feed'=>70,  'align'=>'left',  "font_size"=>7, "height"=>50, "type"=>"image", "file"=>$file);
		$lines[$i][] = array('feed'=>150, 'align'=>'left',  "font_size"=>7, "height"=>50, "text"=>$title);
		$lines[$i][] = array('feed'=>450, 'align'=>'left',  "font_size"=>7, "height"=>50, "text"=>$price);
		$lines[$i][] = array('feed'=>500, 'align'=>'left',  "font_size"=>7, "height"=>50, "text"=>$total);
// }
		$lineBlock = array(
			'lines'  => $lines,
			'height' => 12 
		);
		
		$page = $pdf->drawLineBlocks($page, array($lineBlock), array('table_header' => true));
		// $this->setPage($page);
	}
}
