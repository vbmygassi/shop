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
class Mage_Sales_Model_Order_Pdf_Invoice extends Mage_Sales_Model_Order_Pdf_Abstract
{
    /**
     * Draw header for item table
     *
     * @param Zend_Pdf_Page $page
     * @return void
     */
	
/*
    protected function _drawHeader(Zend_Pdf_Page $page)
    {
        $this->_setFontRegular($page, 20);
        $page->setFillColor(new Zend_Pdf_Color_RGB(0.3, 0.3, 0.3));
        $page->setLineColor(new Zend_Pdf_Color_GrayScale(1));
        $page->setLineWidth(0.5);
        $page->drawRectangle(25, $this->y, 570, $this->y -15);
        $this->y -= 10;
        $page->setFillColor(new Zend_Pdf_Color_RGB(1, 1, 1));

        //columns headers
       
        $lines[0][] = array(
            'text'  => Mage::helper('sales')->__('Art.Nr.:'),
            'feed'  => 35,
            'align' => "left",
		"font_size"=>7 
        );

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
            'feed'  => 450,
            'align' => 'left',
		"font_size"=>7
        );
       
	$lines[0][] = array(
            'text'  => Mage::helper('sales')->__("Gesamt"),
            'feed'  => 500,
            'align' => 'left',
		"font_size"=>7
        );

	$lineBlock = array(
            'lines'  => $lines,
            'height' => 5
        );

        $page = $this->drawLineBlocks($page, array($lineBlock), array('table_header' => true));
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
        $this->y -= 50;
    }

*/

    /**
     * Return PDF document
     *
     * @param  array $invoices
     * @return Zend_Pdf
     */
		

	// das tut so gut
	public function getPdf($invoices=array())
	{
		// was ist denn das?
		$this->_beforeGetPdf();

		// das ist der versteckter "renderer"; der aus irgendeiner configdatei (irgendo, irgendwie) dann aus dem context hervorgeht..
		$this->_initRenderer('invoice');

		// ?? 
		$pdf = new Zend_Pdf();
		$this->_setPdf($pdf);

		// 
		$style = new Zend_Pdf_Style();
        	
		// why not
		// $this->_setFontBold($style, 70);

		// multiple invoices... 
		foreach ($invoices as $invoice) {

			if ($invoice->getStoreId()) {
				// was ist das? was emulate??
				Mage::app()->getLocale()->emulate($invoice->getStoreId());

				// set current store (?id) zu der current store?
				// ich nehme mal an, es geht um mehrsprachigkeit, übersetzungen, i18n, locale
				Mage::app()->setCurrentStore($invoice->getStoreId());
			}

			// === intial routines ===
			// the current "sale" or "order"
			$sale = $invoice->getOrder();
			// the cursor	
			$cursor = new Cursor($this->newPage());
			// the customer
			$customer = Mage::getModel("customer/customer")->load($invoice->getCustomerId());

			// === foot ===
			// feet go first
			$cursor = $this->addFusszeile($cursor);
			
			// === logo ===
			// evaluates path of the logo image
			$path = Mage::getBaseDir("media") . "/sales/store/logo/" . Mage::getStoreConfig("sales/identity/logo");
			// sets cursor to the upper right corner 
			$cursor = $this->setCursor($cursor, $cursor->xmax, $cursor->ymax);
			// evaluates new cursor after logo is added 
			$cursor = $this->addImage($bounds = new ImageDescriptor($cursor, $path, 275, 50, false));
	
			// === rechnungsnummer ===
			// sets cursor
			// adds headline
			$cursor = $this->setCursor($cursor, $cursor->xmin, $cursor->y -75);	
			$cursor = $this->addRechnungsnummer($cursor, $invoice->getIncrementId());	

			// === bestellnummer ===
			// sets cursor
			// adds bestellnummer
			$cursor = $this->setCursor($cursor, $cursor->xmin, $cursor->y -2);
			$cursor = $this->addBestellnummer($cursor, $sale->getRealOrderId());
		
			// === bestelldatum ====
			// sets cursor
			// adds bestelldatum 
			$cursor = $this->setCursor($cursor, $cursor->xmin, $cursor->y -2);
			$datum = Mage::helper('core')->formatDate($sale->getCreatedAt(), 'medium', false);
			$cursor = $this->addBestelldatum($cursor, $datum);
		
			// === rechnungsdatum ===
			// adds rechnungsdatum 
			$cursor = $this->setCursor($cursor, $cursor->xmin, $cursor->y -2);
			$datum = Mage::helper('core')->formatDate($invoice->getCreatedAt(), 'medium', false);
			$cursor = $this->addRechnungsdatum($cursor, $datum);
	
			// === message =========
			$name = $customer->getName();
			$rechnungnr = $invoice->getIncrementId();
			$bestellnr = $sale->getRealOrderId();
			$cursor = $this->setCursor($cursor, $cursor->xmin, $cursor->y -24);
			$cursor = $this->addMessage($cursor, $name, $bestellnr, $rechnungnr);
	
			// === rabattcode =====
			$code = $customer->getInvoiceCoupon();
			$cursor = $this->setCursor($cursor, $cursor->xmin, $cursor->y -24);
			$cursor = $this->addRabattcode($cursor, $code);		
			
			// === empfänger ======
			$billing = $sale->getBillingAddress()->getData();
			$shipping = $sale->getShippingAddress()->getData();
			$cursor = $this->setCursor($cursor, $cursor->xmin, $cursor->y -12);
			$cursor = $this->addEmpfaenger($cursor, $billing, $shipping);
		
			// === artikel ======= 
			$cursor = $this->setCursor($cursor, $cursor->xmin, $cursor->y -12);
			$cursor = $this->addItems($cursor, $invoice->getAllItems());

			// === zahlart =======
			$cursor = $this->setCursor($cursor, $cursor->xmin, $cursor->y -12);
			$cursor = $this->addZahlart($cursor, $sale);	
		}

		// was ist denn das?	
		$this->_afterGetPdf();

		// returns a "pdf" "object" /whatever... 
		return $pdf;
	}
	
	// adds a static fusszeile on each page
	private function addFusszeile($cursor)
	{
		$cursor = $this->setCursor($cursor, $cursor->xmin, $cursor->ymax);
		$font = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA);
		$cursor->page->setFont($font, 7);
		//
		$ypos = $cursor->y -725;
		// 
		$cursor->page->drawText("MyGassi GmbH", $cursor->x, $ypos, "UTF-8");
		$cursor->page->drawText("Nymphenburgerstr. 86", $cursor->x, $ypos -8, "UTF-8");
		$cursor->page->drawText("D-80636 München", $cursor->x, $ypos -16, "UTF-8");
		$cursor->page->drawText("support@mygassi.com", $cursor->x, $ypos -24, "UTF-8");
		$cursor->page->drawText("www.mygassi.com", $cursor->x, $ypos -32, "UTF-8");
		// 
		$cursor->page->drawText("Sitz der Gesellschaft ist München", $cursor->x +100, $ypos, "UTF-8");
		$cursor->page->drawText("HRB 204049", $cursor->x +100, $ypos -8, "UTF-8");
		$cursor->page->drawText("Ust-IdNr: DE288496229", $cursor->x +100, $ypos -16, "UTF-8");
		$cursor->page->drawText("IBAN: DE60 700202700 15233881", $cursor->x +100, $ypos -24, "UTF-8");
		$cursor->page->drawText("SWIFT (BIC): HYVEDEMMXXX", $cursor->x +100, $ypos -32, "UTF-8");
		// 
		$cursor->page->drawText("Geschäftsführung: Sven Claar, Alexander Holzreiter", $cursor->x +240, $ypos, "UTF-8");
		$cursor->page->drawText("Bankverbindung: Hypovereinsbank München", $cursor->x +240, $ypos -8, "UTF-8");
		$cursor->page->drawText("KTO: 15233881", $cursor->x +240, $ypos -16, "UTF-8");
		$cursor->page->drawText("BLZ: 70020270", $cursor->x +240, $ypos -24, "UTF-8");
		// 
		return $cursor;
	}
	
	// adds zahlart
	private function addZahlart($cursor, $sale)
	{
		// checks height of that box (switches to the *next page)
		if($cursor->y -200 < $cursor->ymin){
			$cursor = $this->setCursor($cursor, $cursor->xmin, -1);	
		}
		// 
		$payment = $sale->getPayment()->getMethodInstance()->getTitle();
		$kontonummer = "---";
		$blz = "---"; // ???? was ist denn das schon wieder??? kontonummer???
		$vorgangsnummer = $sale->getPayment()->getLastTransId();
		// 
		$w = ($cursor->xmax -$cursor->xmin) /2;
		$h = 30;	
		// 
		// grauer balken
		$cursor->page->setFillColor(new Zend_Pdf_Color_Rgb(0.3, 0.3, 0.3));
		$cursor->page->setLineColor(new Zend_Pdf_Color_GrayScale(0.9));
		$cursor->page->setLineWidth(0.5);
		$cursor->page->drawRectangle($cursor->x, $cursor->y, $cursor->x +$w, $cursor->y -$h);
		// grüner balken
		$cursor->page->setFillColor(new Zend_Pdf_Color_Html("#58a300"));
		$cursor = $this->setCursor($cursor, $cursor->x +$w, $cursor->y);
		$cursor->page->drawRectangle($cursor->x, $cursor->y, $cursor->x +$w, $cursor->y -$h);
		// überschriften
		$font = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA);
		$cursor->page->setFont($font, 12);
		$cursor->page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
		$cursor = $this->setCursor($cursor, $cursor->xmin, $cursor->y -18);
		$cursor->page->drawText("Zahlart", $cursor->x +12, $cursor->y, "UTF-8");
		$cursor = $this->setCursor($cursor, $cursor->x +$w, $cursor->y);
		$cursor->page->drawText("Betrag", $cursor->x +12, $cursor->y, "UTF-8");
		// linien 
		$cursor = $this->setCursor($cursor, $cursor->xmin, $cursor->y -12);
		$cursor->page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
		$h = 70;
        	$cursor->page->drawRectangle($cursor->xmin, $cursor->y, $cursor->x +$w, $cursor->y -$h);
        	// $cursor->page->drawRectangle($cursor->xmin +$w, $cursor->y, $cursor->xmax, $cursor->y -$h);
		//
		$cursor->page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
		$cursor->page->setFont($font, 8);
		// payment type 
		$cursor = $this->setCursor($cursor, $cursor->xmin, $cursor->y -12);
		$cursor->page->drawText($payment, $cursor->x +12, $cursor->y, "UTF-8");
		$ph = $cursor->y;
		// kontonummer
		$cursor = $this->setCursor($cursor, $cursor->xmin, $cursor->y -24);
		$cursor->page->drawText("Kontonummer:", $cursor->x +12, $cursor->y, "UTF-8");
		$cursor = $this->setCursor($cursor, $cursor->x +80, $cursor->y);
		$cursor->page->drawText($kontonummer, $cursor->x +12, $cursor->y, "UTF-8");
		// blz
		$cursor = $this->setCursor($cursor, $cursor->xmin, $cursor->y -12);
		$cursor->page->drawText("Bankleitzahl:", $cursor->x +12, $cursor->y, "UTF-8");
		$cursor = $this->setCursor($cursor, $cursor->x +80, $cursor->y);
		$cursor->page->drawText($blz, $cursor->x +12, $cursor->y, "UTF-8");
		// vorgangsnummer
		$cursor = $this->setCursor($cursor, $cursor->xmin, $cursor->y -12);
		$cursor->page->drawText("Vorgangsnummer:", $cursor->x +12, $cursor->y, "UTF-8");
		$cursor = $this->setCursor($cursor, $cursor->x +80, $cursor->y);
		$cursor->page->drawText($vorgangsnummer, $cursor->x +12, $cursor->y, "UTF-8");
		// versandart
		$cursor = $this->setCursor($cursor, $cursor->x, $cursor->y -10);
		$message = "Versand durch einen Kurierdienst";
		$sm = $sale->getShippingMethod();
		switch($sm){
			case "freeshipping_freeshipping":
				$message .= " : Versandkostenfrei";
				break;
		};
		// 
		$w = ($cursor->xmax -$cursor->xmin) /2;
		$h = 30;	
		// 
		// grauer balken
		$cursor->page->setFillColor(new Zend_Pdf_Color_Rgb(0.3, 0.3, 0.3));
		$cursor->page->setLineColor(new Zend_Pdf_Color_GrayScale(0.9));
		$cursor->page->setLineWidth(0.5);
		$cursor->page->drawRectangle($cursor->xmin, $cursor->y, $cursor->xmin +$w, $cursor->y -$h);
		$cursor = $this->setCursor($cursor, $cursor->xmin, $cursor->y -$h);
		$cursor->page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
		// linie
		$h = 70;
        	$cursor->page->drawRectangle($cursor->xmin, $cursor->y, $cursor->x +$w, $cursor->y -$h);
		// text	
		$font = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA);
		$cursor->page->setFont($font, 12);
		$cursor->page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
		$cursor->page->drawText("Versandart", $cursor->xmin +12, $cursor->y +12, "UTF-8");
		// linien 
		$cursor = $this->setCursor($cursor, $cursor->xmin, $cursor->y -12);
		$cursor->page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
		$cursor = $this->setCursor($cursor, $cursor->xmin, $cursor->y -12);
		$cursor->page->setFont($font, 8);
		$cursor->page->drawText($message, $cursor->x +12, $cursor->y, "UTF-8");
		//
		$cursor = $this->setCursor($cursor, $cursor->xmin +$w, $ph);
		// netto 
		$preisohnesteuer = $sale->getSubtotal();	
		$preisohnesteuer = Mage::helper("core")->currency($preisohnesteuer, true, false);
		$width = $this->widthForStringUsingFontSize("Gesamt(zzgl. MwSt.):", $font, 8);
		$cursor->page->drawText("Gesamt(zzgl. MwSt.):", $cursor->xmax -100 -$width, $cursor->y, "UTF-8");
		$cursor = $this->setCursor($cursor, $cursor->xmin +$w +100, $cursor->y);
		$width = $this->widthForStringUsingFontSize($preisohnesteuer, $font, 8);
		$cursor->page->drawText($preisohnesteuer, $cursor->xmax -$width -20, $cursor->y, "UTF-8");
		
		// steuer
		$steuer = $sale->getTaxAmount(); 
		$steuer = Mage::helper("core")->currency($steuer, true, false);
		$cursor = $this->setCursor($cursor, $cursor->xmin +$w, $cursor->y -24);
		$width = $this->widthForStringUsingFontSize("(19% MwSt.):", $font, 8);
		$cursor->page->drawText("(19% MwSt.):", $cursor->xmax -100 -$width, $cursor->y, "UTF-8");
		$cursor = $this->setCursor($cursor, $cursor->xmin +$w +100, $cursor->y);
		$width = $this->widthForStringUsingFontSize($steuer, $font, 8);
		$cursor->page->drawText($steuer, $cursor->xmax -$width -20, $cursor->y, "UTF-8");
		
		// rabatt
		$dsc = $sale->getDiscountDescription();
		$dsb = $sale->getBaseDiscountAmount();
		$dsb = Mage::helper("core")->currency($dsb);
		$dsb = str_replace("</span>", "", $dsb);
		$dsb = str_replace('<span class="price">', "", $dsb);
		if(1 < strlen($dsc)){
			$cursor = $this->setCursor($cursor, $cursor->xmin +$w, $cursor->y -24);
			$width = $this->widthForStringUsingFontSize($dsc, $font, 8);
			$cursor->page->drawText($dsc, $cursor->xmax -100 -$width, $cursor->y, "UTF-8");
			$cursor = $this->setCursor($cursor, $cursor->xmin +$w +100, $cursor->y);
			$width = $this->widthForStringUsingFontSize($dsb, $font, 8);
			$cursor->page->drawText($dsb, $cursor->xmax -$width -20, $cursor->y, "UTF-8");
		}

		// zwischensumme
		/*
		$zwischensumme = $sale->getGrandTotal();
		$zwischensumme = Mage::helper("core")->currency($zwischensumme, true, false);
		$cursor = $this->setCursor($cursor, $cursor->xmin +$w, $cursor->y +48);
		$width = $this->widthForStringUsingFontSize("Zwischensumme:", $font, 8);
		$cursor->page->drawText("Zwischensumme:", $cursor->xmax -100 -$width, $cursor->y, "UTF-8");
		$cursor = $this->setCursor($cursor, $cursor->xmin +$w +100, $cursor->y);
		$width = $this->widthForStringUsingFontSize($zwischensumme, $font, 8);
		$cursor->page->drawText($zwischensumme, $cursor->xmax -$width -20, $cursor->y, "UTF-8");
		*/
		
		// total
		$gesamt = $sale->getGrandTotal();
		$gesamt = Mage::helper("core")->currency($gesamt, true, false);
		$font = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_BOLD);
		$width = $this->widthForStringUsingFontSize("Gesamt (inkl. MwSt.):", $font, 8);
		$cursor->page->setFont($font, 8);
		$cursor = $this->setCursor($cursor, $cursor->xmin +$w +100, $cursor->y -24);
		$cursor->page->drawText("Gesamt (inkl. MwSt.):", $cursor->xmax -100 -$width, $cursor->y, "UTF-8");
    		//// rechtsbündig
		$width = $this->widthForStringUsingFontSize($gesamt, $font, 8);
		$cursor->page->drawText($gesamt, $cursor->xmax -$width -20, $cursor->y, "UTF-8");
		//
		return $cursor;	
	}
	
	// adds items
	private function addItems($cursor, $items)
	{
		// header
		$cursor->page->setFillColor(new Zend_Pdf_Color_Rgb(0.3, 0.3, 0.3));
		$cursor->page->setLineColor(new Zend_Pdf_Color_GrayScale(0.9));
		$cursor->page->setLineWidth(0.5);
		$w = $cursor->xmax -$cursor->xmin;
		$h = 20;	
		$font = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA);
		$cursor->page->drawRectangle($cursor->x, $cursor->y, $cursor->x +$w, $cursor->y -$h);
		$cursor->page->setFont($font, 12);
		$cursor = $this->setCursor($cursor, $cursor->xmin, $cursor->y -12);
		$cursor->page->setFont($font, 8);
		$cursor->page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
		$cursor->page->drawText("Anzahl", $cursor->xmin +12, $cursor->y, "UTF-8");
		$cursor->page->drawText("Artikelbezeichnung", $cursor->xmin +120, $cursor->y, "UTF-8");
		$cursor->page->drawText("Stückpreis", $cursor->xmin +420, $cursor->y, "UTF-8");
		$cursor->page->drawText("Gesamt", $cursor->xmin +470, $cursor->y, "UTF-8");
		// items
		$cursor = $this->setCursor($cursor, $cursor->xmin, $cursor->y -32);
		//  
		foreach($items as $item){
		$i = 1; 
		while($i--){	
			$cursor->page->setFillColor(new Zend_Pdf_Color_Rgb(.3, .3, .3));
			// product
			if(false === $prod = Mage::getModel("catalog/product")->loadByAttribute("sku", $item->getSku())){
				continue;
			}
			$qty = round($item->getQty());
			// $title = Mage::helper('core/string')->str_split($item->getName(), 70, true, true);
			$title = $item->getName();
			$prod->load($prod->getId());
			$file = "";
			foreach($prod->getMediaGalleryImages() as $image){
				$path = Mage::helper("catalog/image")->init($prod, "thumbnail", $image->getFile())->keepFrame(false)->resize(360);
				$file = (string)$path;
				$file = str_replace(Mage::getBaseUrl(), "/", $file);
				$file = Mage::getBaseDir() . $file;
			}
			$price = $item->getPriceInclTax();
			$price = Mage::helper("core")->currency($price, true, false);
			$total = $item->getRowTotal() +$item->getTaxAmount();
			$total = Mage::helper("core")->currency($total, true, false);
			// 
			$cursor->page->drawText($qty, $cursor->xmin +12, $cursor->y, "UTF-8");
			$cursor->page->drawText($title, $cursor->xmin +120, $cursor->y, "UTF-8");
			$cursor->page->drawText($price, $cursor->xmin +420, $cursor->y, "UTF-8");
			$cursor->page->drawText($total, $cursor->xmin +470, $cursor->y, "UTF-8");
			$cursor = $this->setCursor($cursor, $cursor->xmin +32, $cursor->y +12);
        		// 
			$w = 75;
			$h = 35;
			$cursor->page->setFillColor(new Zend_Pdf_Color_Rgb(1, 1, 1));
			$cursor->page->setLineColor(new Zend_Pdf_Color_GrayScale(0.9));
			$cursor->page->setLineWidth(0.5);
			$cursor->page->drawRectangle($cursor->x, $cursor->y, $cursor->x +$w, $cursor->y -$h);
			// 
			$cursor = $this->addImage($bounds = new ImageDescriptor($cursor, $file, $w, $h, true));
			$cursor = $this->setCursor($cursor, $cursor->xmin, $cursor->y -50);
		}}
		return $cursor;
	}
	
	// adds receiver
	private function addEmpfaenger($cursor, $billing, $shipping)
	{
		// grauer block links 
		$cursor->page->setFillColor(new Zend_Pdf_Color_Rgb(0.3, 0.3, 0.3));
		$cursor->page->setLineColor(new Zend_Pdf_Color_GrayScale(0.9));
		$cursor->page->setLineWidth(0.5);
		$w = ($cursor->xmax -$cursor->xmin) /2;
		$h = 30;
		$cursor->page->drawRectangle($cursor->x, $cursor->y, $cursor->x +$w, $cursor->y -$h);
		// grauer block rechts 
		$cursor = $this->setCursor($cursor, $cursor->x +$w, $cursor->y);
		$cursor->page->drawRectangle($cursor->x, $cursor->y, $cursor->x +$w, $cursor->y -$h);
		// linien drumherum
		$cursor = $this->setCursor($cursor, $cursor->xmin +12, $cursor->y -24);
		$cursor->page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
        	$cursor->page->drawRectangle($cursor->xmin, $cursor->y, $cursor->xmin +$w, 420);
        	$cursor->page->drawRectangle($cursor->xmin +$w, $cursor->y, $cursor->xmax, 420);
		// grauer block links 
		// text links 
		$font = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA);
        	$cursor->page->setFont($font, 12);
		$cursor = $this->setCursor($cursor, $cursor->xmin +12, $cursor->y +8);
		$cursor->page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
		$cursor->page->drawText(Mage::helper("sales")->__("Empfänger"), $cursor->x, $cursor->y, "UTF-8");
		// text rechts
		$cursor = $this->setCursor($cursor, $cursor->xmin +$w +12, $cursor->y);
		$cursor->page->drawText(Mage::helper("sales")->__("Lieferadresse"), $cursor->x, $cursor->y, "UTF-8");
		// billing || shipping
		$cursor = $this->setCursor($cursor, $cursor->xmin , $cursor->y +12);
		$font = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA);
        	$cursor->page->setFont($font, 8);
		$cursor->page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
		//// company
		$cursor = $this->setCursor($cursor, $cursor->xmin +12, $cursor->y -36);
		$cursor->page->drawText($billing["company"], $cursor->x, $cursor->y, "UTF-8");
		$cursor = $this->setCursor($cursor, $cursor->xmin +$w +12, $cursor->y);
		$cursor->page->drawText($shipping["company"], $cursor->x, $cursor->y, "UTF-8");
		//// name
		$cursor = $this->setCursor($cursor, $cursor->xmin +12, $cursor->y -12);
		$cursor->page->drawText($billing["firstname"] . " " . $billing["lastname"], $cursor->x, $cursor->y, "UTF-8");
		$cursor = $this->setCursor($cursor, $cursor->xmin +$w +12, $cursor->y);
		$cursor->page->drawText($shipping["firstname"] . " " . $shipping["lastname"], $cursor->x, $cursor->y, "UTF-8");
		//// addresse
		$cursor = $this->setCursor($cursor, $cursor->xmin +12, $cursor->y -12);
		$cursor->page->drawText($billing["street"], $cursor->x, $cursor->y, "UTF-8");
		$cursor = $this->setCursor($cursor, $cursor->xmin +$w +12, $cursor->y);
		$cursor->page->drawText($shipping["street"], $cursor->x, $cursor->y, "UTF-8");
		//// zip
		$cursor = $this->setCursor($cursor, $cursor->xmin +12, $cursor->y -12);
		$cursor->page->drawText($billing["postcode"] . " " . $billing["city"], $cursor->x, $cursor->y, "UTF-8");
		$cursor = $this->setCursor($cursor, $cursor->xmin +$w +12, $cursor->y);
		$cursor->page->drawText($shipping["postcode"] . " " . $shipping["city"], $cursor->x, $cursor->y, "UTF-8");
		//// country 
		$cursor = $this->setCursor($cursor, $cursor->xmin +12, $cursor->y -12);
		$cursor->page->drawText(Mage::getModel("directory/country")->loadByCode($billing["country_id"])->getName(), $cursor->x, $cursor->y, "UTF-8");
		$cursor = $this->setCursor($cursor, $cursor->xmin +$w +12, $cursor->y);
		$cursor->page->drawText(Mage::getModel("directory/country")->loadByCode($shipping["country_id"])->getName(), $cursor->x, $cursor->y, "UTF-8");
		// telefon
		$cursor = $this->setCursor($cursor, $cursor->xmin +12, $cursor->y -12);
		$cursor->page->drawText($billing["telephone"], $cursor->x, $cursor->y, "UTF-8");
		$cursor = $this->setCursor($cursor, $cursor->xmin +$w +12, $cursor->y);
		$cursor->page->drawText($shipping["telephone"], $cursor->x, $cursor->y, "UTF-8");
		// fax
		$cursor = $this->setCursor($cursor, $cursor->xmin +12, $cursor->y -12);
		$cursor->page->drawText($billing["fax"], $cursor->x, $cursor->y, "UTF-8");
		$cursor = $this->setCursor($cursor, $cursor->xmin +$w +12, $cursor->y);
		$cursor->page->drawText($shipping["fax"], $cursor->x, $cursor->y, "UTF-8");
		//  
		return $cursor;
	}
	
	// adds ribatt code
	private function addRabattcode($cursor, $code)
	{
		if(1 > strlen($code)){
			return $cursor;
		}
		$font = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA);
        	$cursor->page->setFont($font, 8);
		$cursor->page->drawText("Ihr Rabattcode für den nächsten Einkauf lautet:", $cursor->x, $cursor->y, "UTF-8");
		$cursor = $this->setCursor($cursor, $cursor->xmin, $cursor->y -12);
		$font = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_BOLD);
        	$cursor->page->setFont($font, 8);
		$cursor->page->drawText($code, $cursor->x, $cursor->y, "UTF-8");
		$cursor->y -=8;
		return $cursor;
	}	
	
	// adds message
	private function addMessage($cursor, $name, $bestellnr, $rechnungnr)
	{
		$font = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA);
        	$cursor->page->setFont($font, 8);
		// greetz
		$greetz = "Guten Tag " . $name . ",";
		if("" == $name || null == $name){
			$greetz = "Guten Tag,";
		}
		$cursor->page->drawText($greetz, $cursor->x, $cursor->y, "UTF-8");
		// line1	
		$cursor = $this->setCursor($cursor, $cursor->xmin, $cursor->y -24);
		$gesabbel1  = "vielen Dank für Ihre Bestellung bei MyGassi. Im Anhang finden Sie Ihre Rechnung Nr. " . $rechnungnr;
		$gesabbel1 .= " ";
		$gesabbel1 .= "für Ihre Bestellung Nr. " . $bestellnr . ".";
		$cursor->page->drawText($gesabbel1, $cursor->x, $cursor->y, "UTF-8");
		// line2
		$cursor = $this->setCursor($cursor, $cursor->xmin, $cursor->y -12);
		$gesabbel2  = "Falls Sie Fragen oder Anregungen haben, kontaktieren Sie uns bitte per E-Mail an: support@mygassi.com.";
		$cursor->page->drawText($gesabbel2, $cursor->x, $cursor->y, "UTF-8");
		// line3
		$cursor = $this->setCursor($cursor, $cursor->xmin, $cursor->y -24);
		$gesabbel3 = "Vielen Dank für Ihr Vertrauen! www.mygassi.com";
		$cursor->page->drawText($gesabbel3, $cursor->x, $cursor->y, "UTF-8");
		// 
		return $cursor;
	}
	
	// adds invoice date
	private function addRechnungsdatum($cursor, $message)
	{
		$m = Mage::helper("sales")->__("Rechnungsdatum:");	
		$font = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_BOLD);
        	$cursor->page->setFont($font, 8);
		$cursor->page->drawText($m, $cursor->x, $cursor->y, "UTF-8");
		$cursor->x +=80;
		$cursor->page->drawText($message, $cursor->x, $cursor->y, "UTF-8");
		$cursor->y -=10;
		return $cursor;
	}
	
	// adds order date
	private function addBestelldatum($cursor, $message)
	{
		$m = Mage::helper("sales")->__("Bestelldatum:");	
		$font = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_BOLD);
        	$cursor->page->setFont($font, 8);
		$cursor->page->drawText($m, $cursor->x, $cursor->y, "UTF-8");
		$cursor->x +=80;
		$cursor->page->drawText($message, $cursor->x, $cursor->y, "UTF-8");
		$cursor->y -=9;
		return $cursor;
	}
	
	// adds the bestellnummer
	private function addBestellnummer($cursor, $message)
	{
		$m = Mage::helper("sales")->__("Bestell Nr.");	
		$font = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_BOLD);
        	$cursor->page->setFont($font, 8);
		$cursor->page->drawText($m, $cursor->x, $cursor->y, "UTF-8");
		$cursor->x +=80;
		$cursor->page->drawText($message, $cursor->x, $cursor->y, "UTF-8");
		$cursor->y -=9;
		return $cursor;
	}
	
	// adds the rechnungsnummer
	private function addRechnungsnummer($cursor, $message)
	{
		$message = Mage::helper("sales")->__('Invoice') . " Nr. " . $message;	
		$font = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_BOLD);
        	$cursor->page->setFont($font, 12);
		$cursor->page->drawText($message, $cursor->x, $cursor->y, "UTF-8");
		$cursor->y -=12;
		return $cursor;
	}
	
	// sets the cursor to a desired position: checks for bounds	
	private function setCursor($cursor, $x, $y)
	{
		$cursor->x = $x;
		$cursor->y = $y;
		// check for y bound
		if($cursor->y <= $cursor->ymin){
			$cursor->page = $this->newPage(); 
			$cursor->y = $cursor->ymax;
			$font = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA);
			$cursor->page->setFont($font, 8);
			$cursor = $this->addFusszeile($cursor);
		}
		return $cursor;	
	}
	
	// 	
	// draws an image with a given description into the given context
	private function addImage($bounds)
	{
		// $bounds->path = "/Users/vico/Desktop/err.png";
		// returns without a file
		if(!is_file($bounds->path)){
			return $bounds->cursor;
		}
		// returns without an image
		if(false === ($image = Zend_Pdf_Image::imageWithPath($bounds->path))){
			return $bounds->cursor;
		}
		// evaluates width and height of image
		$w = $image->getPixelWidth();
		$h = $image->getPixelHeight();
		// evaluates max width and height (at a given ratio)
		if($w > $bounds->maxWidth){
			$r = $w /$bounds->maxWidth;
			$w /=$r;
			$h /=$r;
		} 
		// evaluates max height	
		if($h > $bounds->maxHeight){
			$r = $h /$bounds->maxHeight;
			$w /=$r;
			$h /=$r;	
		}
		// adjusts position of cursor 
		if(($bounds->cursor->x +$w) > $bounds->cursor->xmax){
			$bounds->cursor->x = $bounds->cursor->xmax -$w;	
		}
		if($bounds->cursor->x < $bounds->cursor->xmin){
			$bounds->cursor->x = $bounds->cursor->xmin;
		}
		
		// evaluates margins
		$xm = 0;
		if($bounds->center){
			$xm = ($bounds->maxWidth -$w) /2; 
		}
		// $ym = $bounds->maxHeight -$h;
 
		// draws image into given page context
		$bounds->cursor->page->drawImage(
			$image, 
			$bounds->cursor->x +$xm,  
			$bounds->cursor->y -$h, 
			$bounds->cursor->x +$w +$xm, 
			$bounds->cursor->y
		);
		// sets cursor 
		$bounds->cursor->x += $w;	
		// $bounds->cursor->y -= $h;
		// returns bounds	
		return $bounds->cursor;
	}

	/*
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

		// adds logo image
		$this->insertLogo($page, $invoice->getStore());

		// adds address
	 	$this->insertAddress($page, $invoice->getStore());
           
		// adds head
		$this->insertOrder(
			$page,
			$order,
			Mage::getStoreConfigFlag(self::XML_PATH_SALES_PDF_INVOICE_PUT_ORDER_ID, $order->getStoreId())
		);
		
		// sets the font to bold, 9
		$this->_setFontBold($page, 9);
		// adds rechnungsdatum
		$page->drawText(
			Mage::helper('sales')->__('Rechnungsdatum: ') . Mage::helper('core')->formatDate($invoice->getCreatedAt(), 'medium', false),
			35,
			695,
			'UTF-8'
		);

		// adds supercouponcode
		// todo: evaluate whether or not customer has got a coupon code to lower the price of the next sale
		$customer = Mage::getModel("customer/customer")->load($invoice->getCustomerId());
		$coupon = $customer->getInvoiceCoupon();
		$page->drawText(
			"Der 5€ Rabattcode für Ihren nächsten Einkauf lautet: ". $coupon, 
			300, 
			695,
			"UTF-8"
		);

		// adds document text and number
		$this->insertDocumentNumber(
			$page,
			Mage::helper('sales')->__('Invoice # ') . $invoice->getIncrementId()
		);
            
           	 $this->_drawHeader($page);
            
		foreach ($invoice->getAllItems() as $item){
                	if ($item->getOrderItem()->getParentItem()) {
				continue;
			}
			$this->_drawItem($item, $page, $order);
			$page = end($pdf->pages);
		}
		
		// adds t
		$page = $this->insertTotals($page, $invoice);
		if ($invoice->getStoreId()) {
			Mage::app()->getLocale()->revert();
            	}

		// adds shipping method
		$this->insertShippingMethod($page, $invoice);
		
		// adds payment type
		$this->insertPaymentType($page, $invoice);

		// adds footer
		$this->insertFooter($page, $invoice);	
        }
	$this->_afterGetPdf();
	return $pdf;
    }
	*/

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
        /*
	if (!empty($settings['table_header'])) {
            $this->_drawHeader($page);
        }
	*/
        return $page;
    }
	
}

class Cursor
{
	public $x;
	public $y;
	public $xmin =  35;
	public $xmax = 570;
	public $ymin = 100;
	public $ymax = 805; 
	public $page = null;
	function __construct($page, $x=35, $y=805)
	{
		$this->page = $page;
		$this->x = $x;
		$this->y = $y;	
	}
}

class ImageDescriptor
{
	public $path;	
	public $cursor;
	public $maxWidth; 
	public $maxHeigth;
	public $center;
	function __construct($cursor, $path, $maxWidth, $maxHeight, $center){
		$this->path = $path;
		$this->cursor = $cursor;
		$this->maxWidth = $maxWidth;
		$this->maxHeight = $maxHeight;
		$this->center = $center;
	}	
}
