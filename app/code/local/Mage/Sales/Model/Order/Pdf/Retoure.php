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
 * Sales Order Invoice PDF model
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Order_Pdf_Retoure extends Mage_Sales_Model_Order_Pdf_Abstract
{
    /**
     * Draw header for item table
     *
     * @param Zend_Pdf_Page $page
     * @return void
     */
    protected function _drawHeader(Zend_Pdf_Page $page)
    {
        /* Add table head */
        $this->_setFontRegular($page, 20);
        $page->setFillColor(new Zend_Pdf_Color_RGB(0.3, 0.3, 0.3));
        $page->setLineColor(new Zend_Pdf_Color_GrayScale(1));
        $page->setLineWidth(0.5);
        $page->drawRectangle(25, $this->y, 570, $this->y -15);
        $this->y -= 10;
        $page->setFillColor(new Zend_Pdf_Color_RGB(1, 1, 1));

        //columns headers
       
	$lines[0][] = array(
		"text" => Mage::helper("sales")->__("Artikelbezeichnung"),
		"feed" => 150,
		"align"=>"left",
		"font_size"=>7
        );

        $lines[0][] = array(
		'text'  => Mage::helper('sales')->__(''),
		'feed'  => 70,
		'align' => 'left',
		"font_size"=>7
        );

        $lines[0][] = array(
		'text'  => Mage::helper('sales')->__('Anzahl'),
		'feed'  => 35,
		'align' => 'left',
		"font_size"=>7
        );

        $lines[0][] = array(
		'text'  => Mage::helper('sales')->__("Stückpreis"),
		'feed'  => 400,
		'align' => 'left',
		"font_size"=>7
        );
       
	$lines[0][] = array(
		'text'  => Mage::helper('sales')->__("Gesamt"),
		'feed'  => 450,
		'align' => 'left',
		"font_size"=>7
        );

	$lines[0][] = array(
		'text'  => Mage::helper('sales')->__("Retourengrund"),
		'feed'  => 500,
		'align' => 'left',
		"font_size"=>7
        );

	$lineBlock = array(
		'lines'  => $lines,
		'height' => 5
        );

        $this->drawLineBlocks($page, array($lineBlock), array('table_header' => true));
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
        $this->y -= 50;
    }

    /**
     * Return PDF document
     *
     * @param  array $invoices
     * @return Zend_Pdf
     */
    public function getPdf($invoices = array())
    {
        $this->_beforeGetPdf();
        $this->_initRenderer('invoice');

        $pdf = new Zend_Pdf();
        $this->_setPdf($pdf);
        $style = new Zend_Pdf_Style();
        $this->_setFontBold($style, 70);

        foreach ($invoices as $invoice) {
		if ($invoice->getStoreId()) {
			Mage::app()->getLocale()->emulate($invoice->getStoreId());
			Mage::app()->setCurrentStore($invoice->getStoreId());
		}
		
		$page  = $this->newPage();
		$order = $invoice->getOrder();

		/* adds the logo */
		$this->insertLogo($page, $invoice->getStore());

		/* adds the address */
	 	$this->insertAddress($page, $invoice->getStore());
           
		// "Was passt denn an der Ware nicht Du Knilch?"
		$this->insertMain($page, $order);

		/* add document text and number */
		$this->insertDocumentNumber(
			$page,
			Mage::helper('sales')->__('Retourenschein # ') . $invoice->getIncrementId()
		);
            
		/* adds table */
           	$this->_drawHeader($page);
	
		foreach ($invoice->getAllItems() as $item){
			
			$lines = array();
			$qty   = round($item->getQty());
			$sku   = $item->getSku();
			
			$title = Mage::helper('core/string')->str_split($item->getName(), 50, true, true);
		
			if(false !== $prod = Mage::getModel("catalog/product")->loadByAttribute("sku", $sku)){
				$prod->load($prod->getId());
				foreach($prod->getMediaGalleryImages() as $image){
					// $thumb = Mage::getBaseDir("media") . "/catalog/product" . $image->getFile();
					$path = Mage::helper("catalog/image")->init($prod, "thumbnail", $image->getFile())->keepFrame(false)->resize(360);
					$file = (string)$path;
					$file = str_replace(Mage::getBaseUrl(), "/", $file);
					$file = Mage::getBaseDir() . $file;
				}
			}	
			
			$price = $order->formatPriceTxt($item->getPriceInclTax());
			$total = $order->formatPriceTxt($item->getRowTotal() +$item->getTaxAmount());
	
			$lines[0][] = array("feed"=>35,  'align'=>'right', "font_size"=>7, "height"=>50, "text"=>$qty);
			$lines[0][] = array('feed'=>70,  'align'=>'left',  "font_size"=>7, "height"=>50, "type"=>"image", "file"=>$file);
			$lines[0][] = array('feed'=>150, 'align'=>'left',  "font_size"=>7, "height"=>50, "text"=>$title);
			$lines[0][] = array('feed'=>400, 'align'=>'left',  "font_size"=>7, "height"=>50, "text"=>$price);
			$lines[0][] = array('feed'=>450, 'align'=>'left',  "font_size"=>7, "height"=>50, "text"=>$total);
			$lines[0][] = array('feed'=>500, 'align'=>'left',  "font_size"=>7, "height"=>50, "type"=>"check");
        		
			$lineBlock = array('lines'=>$lines, 'height'=>50);
        		
			$page = $this->drawLineBlocks($page, array($lineBlock), array('table_header' => true));
		}
		

		/* adds totals */
		$page = $this->insertTotals($page, $invoice);
		if ($invoice->getStoreId()) {
			Mage::app()->getLocale()->revert();
            	}

		// adds shipping method
		// $this->insertShippingMethod($page, $invoice);

		// adds shipping method
        	$page->setFillColor(new Zend_Pdf_Color_Rgb(0.3, 0.3, 0.3));
        	$page->setLineColor(new Zend_Pdf_Color_GrayScale(0.9));
        	$page->setLineWidth(0.5);
        	$page->drawRectangle(25,  $this->sy, 275, ($this->sy -30));
        	$page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
        	$this->_setFontBold($page, 12);
		$page->drawText(Mage::helper('sales')->__('Retourengründe'), 35, ($this->sy -20), 'UTF-8');
		
		// Linien drumherum	
		$page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
        	$page->drawRectangle(25, ($this->sy -30), 275, $this->sy -100);
		$this->py = $this->sy -100;
		$gesabbel = <<<EOD
Bitte tragen Sie die Nummer des Retourengrundes in den Rücksendeschein ein. Diese Angaben sind für Sie natürlich freiwillig.
EOD;
		$this->_setFontRegular($page, 7);
        	$page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
		$offset = 0;
                foreach(Mage::helper('core/string')->str_split($gesabbel, 55, true, true) as $line){
			$page->drawText(Mage::helper('sales')->__($line), 35, $this->sy -50 -$offset, 'UTF-8');
			$offset +=12;
		}

        	$page->setFillColor(new Zend_Pdf_Color_Rgb(0.3, 0.3, 0.3));
        	$page->setLineColor(new Zend_Pdf_Color_GrayScale(0.9));
        	$page->setLineWidth(0.5);
        	
		
		$page->drawRectangle(25,  $this->py, 275, ($this->py -30));
        	$page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
        	

		$this->_setFontBold($page, 12);
		$page->drawText(Mage::helper('sales')->__('Woran liegt es?'), 35, ($this->py -20), 'UTF-8');
		
		// Linien drumherum	
		$page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
        	$page->drawRectangle(25, ($this->py -30), 275, $this->py -160);
		// 
		
		$this->_setFontRegular($page, 7);
        	$page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
		$page->drawText("10. Artikel ist beschädigt. Bitte neu versenden.", 35, ($this->py -45), 'UTF-8');
		$page->drawText("11. Artikel ist beschädigt. (kein Neuversand)", 35, ($this->py -57), 'UTF-8');
		$page->drawText("12. Artikel funktioniert nicht. Bitte neu versenden.", 35, ($this->py -69), 'UTF-8');
		$page->drawText("13. Artikel funktioniert nicht. (kein Neuversand)", 35, ($this->py -81), 'UTF-8');
		$page->drawText("14. Artikel ist anders, als er abgebildet ist.", 35, ($this->py -93), 'UTF-8');
		$page->drawText("15. Artikel hat nicht die Qualität, die ich erwartet habe.", 35, ($this->py -105), 'UTF-8');
		$page->drawText("16. Artikel hat schlechtes Preis / Leistungsverhältnis.", 35, ($this->py -117), 'UTF-8');
		$page->drawText("17. Es wurde zu spät geliefert.", 35, ($this->py -129), 'UTF-8');
		$page->drawText("18. Artikel wurde doppelt geliefert.", 35, ($this->py -141), 'UTF-8');
        
		$this->insertFooter($page, $invoice);
	}
	$this->_afterGetPdf();
	return $pdf;
    }

    /**
     * Create new page and assign to PDF object
     *
     * @param  array $settings
     * @return Zend_Pdf_Page
     */
    public function newPage(array $settings = array())
    {
        /* Add new table head */
        $page = $this->_getPdf()->newPage(Zend_Pdf_Page::SIZE_A4);
        $this->_getPdf()->pages[] = $page;
        $this->y = 800;
        if (!empty($settings['table_header'])) {
            $this->_drawHeader($page);
        }
        return $page;
    }
	
}
