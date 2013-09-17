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
 * Sales Order PDF abstract model
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
// abstract class Mage_Sales_Model_Order_Pdf_Abstract extends Varien_Object
abstract class Mage_Sales_Model_Order_Pdf_Abstract extends Mage_Core_Model_Abstract 
{
    /**
     * Y coordinate
     *
     * @var int
     */
    public $y;

    /**
     * Item renderers with render type key
     *
     * model    => the model name
     * renderer => the renderer model
     *
     * @var array
     */
    protected $_renderers = array();

    /**
     * Predefined constants
     */
    const XML_PATH_SALES_PDF_INVOICE_PUT_ORDER_ID       = 'sales_pdf/invoice/put_order_id';
    const XML_PATH_SALES_PDF_SHIPMENT_PUT_ORDER_ID      = 'sales_pdf/shipment/put_order_id';
    const XML_PATH_SALES_PDF_CREDITMEMO_PUT_ORDER_ID    = 'sales_pdf/creditmemo/put_order_id';

    /**
     * Zend PDF object
     *
     * @var Zend_Pdf
     */
    protected $_pdf;

    /**
     * Default total model
     *
     * @var string
     */
    protected $_defaultTotalModel = 'sales/order_pdf_total_default';

    /**
     * Retrieve PDF
     *
     * @return Zend_Pdf
     */
    abstract public function getPdf();

    /**
     * Returns the total width in points of the string using the specified font and
     * size.
     *
     * This is not the most efficient way to perform this calculation. I'm
     * concentrating optimization efforts on the upcoming layout manager class.
     * Similar calculations exist inside the layout manager class, but widths are
     * generally calculated only after determining line fragments.
     *
     * @param  string $string
     * @param  Zend_Pdf_Resource_Font $font
     * @param  float $fontSize Font size in points
     * @return float
     */
    public function widthForStringUsingFontSize($string, $font, $fontSize)
    {
        $drawingString = '"libiconv"' == ICONV_IMPL ?
            iconv('UTF-8', 'UTF-16BE//IGNORE', $string) :
            @iconv('UTF-8', 'UTF-16BE', $string);

        $characters = array();
        for ($i = 0; $i < strlen($drawingString); $i++) {
            $characters[] = (ord($drawingString[$i++]) << 8) | ord($drawingString[$i]);
        }
        $glyphs = $font->glyphNumbersForCharacters($characters);
        $widths = $font->widthsForGlyphs($glyphs);
        $stringWidth = (array_sum($widths) / $font->getUnitsPerEm()) * $fontSize;
        return $stringWidth;

    }

    /**
     * Calculate coordinates to draw something in a column aligned to the right
     *
     * @param  string $string
     * @param  int $x
     * @param  int $columnWidth
     * @param  Zend_Pdf_Resource_Font $font
     * @param  int $fontSize
     * @param  int $padding
     * @return int
     */
    public function getAlignRight($string, $x, $columnWidth, Zend_Pdf_Resource_Font $font, $fontSize, $padding = 5)
    {
        $width = $this->widthForStringUsingFontSize($string, $font, $fontSize);
        return $x + $columnWidth - $width - $padding;
    }

    /**
     * Calculate coordinates to draw something in a column aligned to the center
     *
     * @param  string $string
     * @param  int $x
     * @param  int $columnWidth
     * @param  Zend_Pdf_Resource_Font $font
     * @param  int $fontSize
     * @return int
     */
    public function getAlignCenter($string, $x, $columnWidth, Zend_Pdf_Resource_Font $font, $fontSize)
    {
        $width = $this->widthForStringUsingFontSize($string, $font, $fontSize);
        return $x + round(($columnWidth - $width) / 2);
    }

    /**
     * Insert logo to pdf page
     *
     * @param Zend_Pdf_Page $page
     * @param null $store
     */
    protected function insertLogo(&$page, $store = null)
    {
        $this->y = $this->y ? $this->y : 815;
        $image = Mage::getStoreConfig('sales/identity/logo', $store);
        
	if ($image) {
            $image = Mage::getBaseDir('media') . '/sales/store/logo/' . $image;
            if (is_file($image)) {
                $image       = Zend_Pdf_Image::imageWithPath($image);
                $top         = 830; //top border of the page
                $widthLimit  = 270; //half of the page width
                $heightLimit = 270; //assuming the image is not a "skyscraper"
                $width       = $image->getPixelWidth();
                $height      = $image->getPixelHeight();

                //preserving aspect ratio (proportions)
                $ratio = $width / $height;
		if ($ratio > 1 && $width > $widthLimit) {
                    $width  = $widthLimit;
                    $height = $width / $ratio;
                } elseif ($ratio < 1 && $height > $heightLimit) {
                    $height = $heightLimit;
                    $width  = $height * $ratio;
                } elseif ($ratio == 1 && $height > $heightLimit) {
                    $height = $heightLimit;
                    $width  = $widthLimit;
                }

		$width  /= 2;
		$height /= 2;
		
		$border = 30;

                $y1 = $top -$height;
                $y2 = $top;
                $x1 = $page->getWidth() -$width -$border;
		$x2 = $page->getWidth() -$border;

                //coordinates after transformation are rounded by Zend
                $page->drawImage($image, $x1, $y1, $x2, $y2);

                $this->y = $y1 - 10;
            }
        }
    }

    /**
     * Insert address to pdf page
     *
     * @param Zend_Pdf_Page $page
     * @param null $store
     */
    protected function insertAddress(&$page, $store = null)
    {
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
        $font = $this->_setFontRegular($page, 10);
        $page->setLineWidth(0);
        $this->y = $this->y ? $this->y : 815;
        $top = 815;
        foreach (explode("\n", Mage::getStoreConfig('sales/identity/address', $store)) as $key=>$value){
            if ($value !== '') {
                $value = preg_replace('/<br[^>]*>/i', "\n", $value);
                foreach (Mage::helper('core/string')->str_split($value, 45, true, true) as $_value) {
                    $page->drawText(trim(strip_tags($_value)),
                        $this->getAlignRight($_value, 130, 440, $font, 10),
                        $top,
                        'UTF-8');
                    $top -= 10;
                }
            }
        }
        $this->y = ($this->y > $top) ? $top : $this->y;
    }

    /**
     * Format address
     *
     * @param  string $address
     * @return array
     */
    protected function _formatAddress($address)
    {
        $return = array();
        foreach (explode('|', $address) as $str) {
            foreach (Mage::helper('core/string')->str_split($str, 45, true, true) as $part) {
                if (empty($part)) {
                    continue;
                }
                $return[] = $part;
            }
        }
        return $return;
    }

    /**
     * Calculate address height
     *
     * @param  array $address
     * @return int Height
     */
    protected function _calcAddressHeight($address)
    {
        $y = 0;
        foreach ($address as $value){
            if ($value !== '') {
                $text = array();
                foreach (Mage::helper('core/string')->str_split($value, 55, true, true) as $_value) {
                    $text[] = $_value;
                }
                foreach ($text as $part) {
                    $y += 15;
                }
            }
        }
        return $y;
    }





	protected function insertMain($page, $sale)
	{
		$this->y = $this->y ? $this->y : 815;
		$top = $this->y;
		$this->setDocHeaderCoordinates(array(25, $top, 570, $top - 55));
		$this->_setFontRegular($page, 7);

		$page->drawText(
			Mage::helper('sales')
				->__('Order #') . $sale->getRealOrderId(), 
			35, 
			$top -=30, 
			'UTF-8'
		);

		$page->drawText(
			Mage::helper('sales')
				->__('Order Date:') . " " . Mage::helper('core')
				->formatDate($sale->getCreatedAtStoreDate(), 'medium', false),
			35,
			$top -=15,
			'UTF-8'
		);
        
		$top -= 10;
		$page->setFillColor(new Zend_Pdf_Color_Rgb(0.3, 0.3, 0.3));
		$page->setLineColor(new Zend_Pdf_Color_GrayScale(0.9));
		$page->setLineWidth(0.5);
		$page->drawRectangle( 25, $top, 570, ($top -30));

		$page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
		$this->_setFontBold($page, 12);
		$page->drawText(Mage::helper("sales")->
			__("Ware gefällt nicht? Umtauschen? - So einfach geht's!"), 35, $top -20, 'UTF-8');

		$page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
		$page->drawRectangle(25, ($top -30), 570, $top -33 -70);

		$this->_setFontRegular($page, 7);
		$page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
		
		$gesabbel = <<<EOD
Bei Mygassi gekaufte Artikel können Sie innerhalb von 2 Wochen jederzeit an uns als Retoure zurücksenden. 
Die Retoure ist für Sie kostenfrei. Bitte benutzen Sie dazu das MyGassi Retourenlabel, 
das Sie unter "https://shop.mygassi.com/retoure" herunterladen und ausdrucken können.
EOD;
		
		$offset = 0;
                foreach(Mage::helper('core/string')->str_split($gesabbel, 105, true, true) as $line){
			$page->drawText(Mage::helper('sales')->__($line), 35, $top -50 -$offset, 'UTF-8');
			$offset +=12;
		}

		$image = Zend_Pdf_Image::imageWithPath(Mage::getBaseDir() . "/skin/frontend/default/mygassi-mobile/images/maya.png");
		$mw = 50;
		$mh = 50;
		$ratio = $image->getPixelWidth() /$mw;
		$w = $image->getPixelWidth() /$ratio;
		$h = $image->getPixelHeight() /$ratio;
		if($h > $maxHeight){
			$ratio = $image->getPixelHeight() /$mh;
			$w = $image->getPixelWidth() /$ratio;
			$h = $image->getPixelHeight() /$ratio;
		}	
		$x1 = 500; 
		$y1 = $top -90;
		$x2 = $x1 +$w;
		$y2 = $y1 +$h;
		$page->drawImage($image, $x1, $y1, $x2, $y2);

		$this->y = 580;

	}











    /**
     * Insert order to pdf page
     *
     * @param Zend_Pdf_Page $page
     * @param Mage_Sales_Model_Order $obj
     * @param bool $putOrderId
     */
    protected function insertOrder(&$page, $obj, $putOrderId = true)
    {
	if ($obj instanceof Mage_Sales_Model_Order) {
            $shipment = null;
            $order = $obj;
        } elseif ($obj instanceof Mage_Sales_Model_Order_Shipment) {
            $shipment = $obj;
            $order = $shipment->getOrder();
        }

        $this->y = $this->y ? $this->y : 815;
        $top = $this->y;

        // $page->setFillColor(new Zend_Pdf_Color_GrayScale(0.45));
        // $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.45));
        // $page->drawRectangle(25, $top, 570, $top - 55);
        // $page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
	$this->setDocHeaderCoordinates(array(25, $top, 570, $top - 55));
        // $this->_setFontRegular($page, 10);
        // $this->_setFontRegular($page, 7);

	// sets the font to bold, 9pt
        $this->_setFontBold($page, 9);
	
	// rechungsnummer
	$page->drawText(
		Mage::helper('sales')->__('Order #') . $order->getRealOrderId(), 
		35, 
		($top -= 30), 
		'UTF-8'
	);

	// datum
        $page->drawText(
		Mage::helper('sales')->__('Order Date: ') . Mage::helper('core')->formatDate($order->getCreatedAtStoreDate(), 'medium', false),
		35,
		($top -= 15),
		'UTF-8'
	);

        $top -= 15;
	$page->setFillColor(new Zend_Pdf_Color_Rgb(0.3, 0.3, 0.3));
        $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.9));
        $page->setLineWidth(0.5);
        $page->drawRectangle( 25, $top, 275, ($top -30));
        $page->drawRectangle(275, $top, 570, ($top -30));
	/* Calculate blocks info */
	
        /* Billing Address */
	/*
	$temp = $order->getBillingAddress();
	unset($temp["telephone"]);
	$billingAddress = $this->_formatAddress($temp->format('pdf'));
	*/

	// format pdf sucks again
	$temp  = $order->getBillingAddress()->getData();
	unset($temp["telephone"]);
	$buff  = "";
	$buff .= $temp["company"];
	$buff .= "|";
	$buff .= $temp["firstname"] . " " . $temp["lastname"];
	$buff .= "|";
	$buff .= $temp["street"];
	$buff .= "|";
	$buff .= $temp["postcode"]. " " . $temp["city"];
	$buff .= "|";
	$buff .= Mage::getModel('directory/country')->loadByCode($temp["country_id"])->getName(); 

	$billingAddress = $this->_formatAddress($buff);
	

        /* Payment */
	$paymentInfo = Mage::helper('payment')->getInfoBlock($order->getPayment())
            ->setIsSecureMode(true)
            ->toPdf();
        $paymentInfo = htmlspecialchars_decode($paymentInfo, ENT_QUOTES);
        $payment = explode('{{pdf_row_separator}}', $paymentInfo);
        foreach ($payment as $key=>$value){
            if (strip_tags(trim($value)) == '') {
                unset($payment[$key]);
            }
        }
        reset($payment);

        /* Shipping Address and Method */
        if (!$order->getIsVirtual()) {
            

		/* Shipping Address */
		/*
		$temp = $order->getShippingAddress();
		unset($temp["telephone"]);
		$shippingAddress = $this->_formatAddress($temp->format('pdf'));
            	*/
	
		// format pdf sucks again
		$temp  = $order->getShippingAddress()->getData();
		unset($temp["telephone"]);
		$buff  = "";
		$buff .= $temp["company"];
		$buff .= "|";
		$buff .= $temp["firstname"] . " " . $temp["lastname"];
		$buff .= "|";
		$buff .= $temp["street"];
		$buff .= "|";
		$buff .= $temp["postcode"]. " " . $temp["city"];
		$buff .= "|";
		$buff .= Mage::getModel('directory/country')->loadByCode($temp["country_id"])->getName(); 
		$shippingAddress = $this->_formatAddress($buff);
		// 
			
		$shippingMethod  = $order->getShippingDescription();
        }

        $page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
        $this->_setFontBold($page, 12);
        $page->drawText(Mage::helper('sales')->__('Empfänger'), 35, ($top -20), 'UTF-8');

        if (!$order->getIsVirtual()) {
            $page->drawText(Mage::helper('sales')->__('Lieferadresse'), 285, ($top -20), 'UTF-8');
        } else {
            // $page->drawText(Mage::helper('sales')->__('Payment Method'), 285, ($top - 15), 'UTF-8');
        }

        $addressesHeight = $this->_calcAddressHeight($billingAddress);
        if (isset($shippingAddress)) {
            $addressesHeight = max($addressesHeight, $this->_calcAddressHeight($shippingAddress));
        }

        





	$page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
        $page->drawRectangle(25, ($top -30), 570, $top -33 -$addressesHeight);
       




 
	$page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
	$this->_setFontRegular($page, 7);
        $this->y = $top - 40;
        $addressesStartY = $this->y;

        foreach ($billingAddress as $value){
            if ($value !== '') {
                $text = array();
                foreach (Mage::helper('core/string')->str_split($value, 45, true, true) as $_value) {
                    $text[] = $_value;
                }
                foreach ($text as $part) {
                    $page->drawText(strip_tags(ltrim($part)), 35, $this->y, 'UTF-8');
                    $this->y -= 15;
                }
            }
        }

        $addressesEndY = $this->y;







        if (!$order->getIsVirtual()) {
            $this->y = $addressesStartY;
            foreach ($shippingAddress as $value){
                if ($value!=='') {
                    $text = array();
                    foreach (Mage::helper('core/string')->str_split($value, 45, true, true) as $_value) {
                        $text[] = $_value;
                    }
                    foreach ($text as $part) {
                        $page->drawText(strip_tags(ltrim($part)), 285, $this->y, 'UTF-8');
                        $this->y -= 15;
                    }
                }
            }

            $addressesEndY = min($addressesEndY, $this->y);
            $this->y = $addressesEndY;


/*
            $page->setFillColor(new Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
            $page->setLineWidth(0.5);
            $page->drawRectangle(25, $this->y, 275, $this->y-25);
            $page->drawRectangle(275, $this->y, 570, $this->y-25);
*/

/*

            $this->y -= 15;
            $this->_setFontBold($page, 12);
            $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
            $page->drawText(Mage::helper('sales')->__('Payment Method'), 35, $this->y, 'UTF-8');
            $page->drawText(Mage::helper('sales')->__('Shipping Method:'), 285, $this->y , 'UTF-8');
*/


/*
            $this->y -=10;
            $page->setFillColor(new Zend_Pdf_Color_GrayScale(1));

            $this->_setFontRegular($page, 10);
            $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));

*/
            $paymentLeft = 35;
            $yPayments   = $this->y - 15;

        }
        else {
  
          $yPayments   = $addressesStartY;
            $paymentLeft = 285;
  
      }




/*
	foreach ($payment as $value){
            if (trim($value) != '') {
                //Printing "Payment Method" lines
                $value = preg_replace('/<br[^>]*>/i', "\n", $value);
                foreach (Mage::helper('core/string')->str_split($value, 45, true, true) as $_value) {
                    $page->drawText(strip_tags(trim($_value)), $paymentLeft, $yPayments, 'UTF-8');
                    $yPayments -= 15;
                }
            }
        }
*/


/*
	if ($order->getIsVirtual()) {
            // replacement of Shipments-Payments rectangle block
            $yPayments = min($addressesEndY, $yPayments);
            $page->drawLine(25,  ($top - 25), 25,  $yPayments);
            $page->drawLine(570, ($top - 25), 570, $yPayments);
            $page->drawLine(25,  $yPayments,  570, $yPayments);

            $this->y = $yPayments - 15;
        } else {
            $topMargin    = 15;
            $methodStartY = $this->y;
            $this->y     -= 15;

            foreach (Mage::helper('core/string')->str_split($shippingMethod, 45, true, true) as $_value) {
                $page->drawText(strip_tags(trim($_value)), 285, $this->y, 'UTF-8');
                $this->y -= 15;
            }

            $yShipments = $this->y;
            $totalShippingChargesText = "(" . Mage::helper('sales')->__('Total Shipping Charges') . " "
                . $order->formatPriceTxt($order->getShippingAmount()) . ")";

            $page->drawText($totalShippingChargesText, 285, $yShipments - $topMargin, 'UTF-8');
            $yShipments -= $topMargin + 10;

            $tracks = array();
            if ($shipment) {
                $tracks = $shipment->getAllTracks();
            }
            if (count($tracks)) {
                $page->setFillColor(new Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
                $page->setLineWidth(0.5);
                $page->drawRectangle(285, $yShipments, 510, $yShipments - 10);
                $page->drawLine(400, $yShipments, 400, $yShipments - 10);
                //$page->drawLine(510, $yShipments, 510, $yShipments - 10);

                $this->_setFontRegular($page, 9);
                $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
                //$page->drawText(Mage::helper('sales')->__('Carrier'), 290, $yShipments - 7 , 'UTF-8');
                $page->drawText(Mage::helper('sales')->__('Title'), 290, $yShipments - 7, 'UTF-8');
                $page->drawText(Mage::helper('sales')->__('Number'), 410, $yShipments - 7, 'UTF-8');

                $yShipments -= 20;
                $this->_setFontRegular($page, 8);
                foreach ($tracks as $track) {

                    $CarrierCode = $track->getCarrierCode();
                    if ($CarrierCode != 'custom') {
                        $carrier = Mage::getSingleton('shipping/config')->getCarrierInstance($CarrierCode);
			$carrierTitle = "";
			if(is_object($carrier)){
				$carrierTitle = $carrier->getConfigData('title');
			}
                    } else {
                        $carrierTitle = Mage::helper('sales')->__('Custom Value');
                    }

                    //$truncatedCarrierTitle = substr($carrierTitle, 0, 35) . (strlen($carrierTitle) > 35 ? '...' : '');
                    $maxTitleLen = 45;
                    $endOfTitle = strlen($track->getTitle()) > $maxTitleLen ? '...' : '';
                    $truncatedTitle = substr($track->getTitle(), 0, $maxTitleLen) . $endOfTitle;
                    //$page->drawText($truncatedCarrierTitle, 285, $yShipments , 'UTF-8');
                    $page->drawText($truncatedTitle, 292, $yShipments , 'UTF-8');
                    $page->drawText($track->getNumber(), 410, $yShipments , 'UTF-8');
                    $yShipments -= $topMargin - 5;
                }
            } else {
                $yShipments -= $topMargin - 5;
            }

            $currentY = min($yPayments, $yShipments);

            // replacement of Shipments-Payments rectangle block
            $page->drawLine(25,  $methodStartY, 25,  $currentY); //left
            $page->drawLine(25,  $currentY,     570, $currentY); //bottom
            $page->drawLine(570, $currentY,     570, $methodStartY); //right

            $this->y = $currentY;
            $this->y -= 15;
        }
    
*/







	}

    /**
     * Insert title and number for concrete document type
     *
     * @param  Zend_Pdf_Page $page
     * @param  string $text
     * @return void
     */
    public function insertDocumentNumber(Zend_Pdf_Page $page, $text)
    {
        // $page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
        $this->_setFontRegular($page, 20);
        $docHeader = $this->getDocHeaderCoordinates();
        $page->drawText($text, 35, $docHeader[1] - 15, 'UTF-8');
    }

    /**
     * Sort totals list
     *
     * @param  array $a
     * @param  array $b
     * @return int
     */
    protected function _sortTotalsList($a, $b) {
        if (!isset($a['sort_order']) || !isset($b['sort_order'])) {
            return 0;
        }

        if ($a['sort_order'] == $b['sort_order']) {
            return 0;
        }

        return ($a['sort_order'] > $b['sort_order']) ? 1 : -1;
    }

    /**
     * Return total list
     *
     * @param  Mage_Sales_Model_Abstract $source
     * @return array
     */
    protected function _getTotalsList($source)
    {
        $totals = Mage::getConfig()->getNode('global/pdf/totals')->asArray();
        usort($totals, array($this, '_sortTotalsList'));
        $totalModels = array();
        foreach ($totals as $index => $totalInfo) {
            if (!empty($totalInfo['model'])) {
                $totalModel = Mage::getModel($totalInfo['model']);
                if ($totalModel instanceof Mage_Sales_Model_Order_Pdf_Total_Default) {
                    $totalInfo['model'] = $totalModel;
                } else {
                    Mage::throwException(
                        Mage::helper('sales')->__('PDF total model should extend Mage_Sales_Model_Order_Pdf_Total_Default')
                    );
                }
            } else {
                $totalModel = Mage::getModel($this->_defaultTotalModel);
            }
            $totalModel->setData($totalInfo);
            $totalModels[] = $totalModel;
        }

        return $totalModels;
    }

    /**
     * Insert totals to pdf page
     *
     * @param  Zend_Pdf_Page $page
     * @param  Mage_Sales_Model_Abstract $source
     * @return Zend_Pdf_Page
     */
	protected function insertTotals($page, $source)
	{

		// remvoeme
		$this->y += 40;

		if ($this->y -150 < 15) {
			$page = $this->newPage($pageSettings);
		}

		$order = $source->getOrder();
		$totals = $this->_getTotalsList($source);
		$lineBlock = array('lines'  => array(), 'height' => 20);

		$this->y -= 30;
		$page->setFillColor(new Zend_Pdf_Color_Html("#58a300"));
        	$page->setLineColor(new Zend_Pdf_Color_GrayScale(1));
        	$page->setLineWidth(0.5);
        	$page->drawRectangle(275, $this->y, 570, $this->y -30);
        	$this->_setFontBold($page, 12);
        	$page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
		$page->drawText(Mage::helper('sales')->__('Betrag'), 285, ($this->y -20), 'UTF-8');
		$this->sy = $this->y;
		$page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
		$this->y -= 30;
		
		foreach ($totals as $total) {
			$total->setOrder($order)->setSource($source);
			if ($total->canDisplay()) {
				$total->setFontSize(10);
				foreach ($total->getTotalsForDisplay() as $totalData) {
					$fontSize = 7;
					if("Gesamt (inkl. Steuern):" === $totalData["label"]){
						$fontSize = 10;
					}
					if(false !== strpos($totalData["label"], "Produkte zu 19")){
						continue;
					}
					if("Steuer:" === $totalData["label"]){
						$totalData["label"] = "19% MwSt.";
					}
					$totalData["label"] = str_replace("Steuern", "MwSt.", $totalData["label"]);
					$lineBlock['lines'][] = array(
						array(
							'text'      => $totalData['label'],
							'feed'      => 475,
							'align'     => 'right',
							"font_size" => 7,
							'font'      => 'bold'
						),
						array(
							'text'      => $totalData['amount'],
							'feed'      => 525,
							'align'     => 'right',
							"font_size" => $fontSize,
							'font'      => 'bold'
                        			),
                    			);
                		}
            		}
        	}
		$this->y -= 20;
		$page = $this->drawLineBlocks($page, array($lineBlock));
		return $page;
	}

	var $sy = 0;
	public function insertShippingMethod($page, $source)
	{
		if ($this->y -150 < 15) {
			$page = $this->newPage($pageSettings);
		}
        	
		$page->setFillColor(new Zend_Pdf_Color_Rgb(0.3, 0.3, 0.3));
        	$page->setLineColor(new Zend_Pdf_Color_GrayScale(0.9));
        	$page->setLineWidth(0.5);
        	$page->drawRectangle(25,  $this->sy, 275, ($this->sy -30));
        	$page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
        	$this->_setFontBold($page, 12);
		$page->drawText(Mage::helper('sales')->__('Versandart'), 35, ($this->sy -20), 'UTF-8');
		// Linien drumherum	
		$page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
        	$page->drawRectangle(25, ($this->sy -30), 275, $this->sy -100);
		$this->py = $this->sy -100;
	
		$gesabbel = "Versand durch einen Kurierdienst";
		$sale = $source->getOrder();
		$sm = $sale->getShippingMethod();
		switch($sm){
			case "freeshipping_freeshipping":
				$gesabbel .= " : Versandkostenfrei";
				break;
		}
		$this->_setFontRegular($page, 7);
        	$page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
		$page->drawText($gesabbel, 35, ($this->sy -45), 'UTF-8');
		
	}

	var $py;
	public function insertPaymentType($page, $source)
	{
        	$page->setFillColor(new Zend_Pdf_Color_Rgb(0.3, 0.3, 0.3));
        	$page->setLineColor(new Zend_Pdf_Color_GrayScale(0.9));
        	$page->setLineWidth(0.5);
        	$page->drawRectangle(25,  $this->py, 275, ($this->py -30));
        	$page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
        	$this->_setFontBold($page, 12);
		$page->drawText(Mage::helper('sales')->__('Zahlart'), 35, ($this->py -20), 'UTF-8');
		// Linien drumherum	
		$page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
        	$page->drawRectangle(25, ($this->py -30), 275, $this->py -100);
	
		$sale = $source->getOrder();
		$payment = "";
		try{
			$payment = $sale->getPayment()->getMethodInstance()->getTitle();
		}
		catch(Exception $e){}
        	
		$this->_setFontRegular($page, 7);
        	$page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
		$page->drawText($payment, 35, ($this->py -45), 'UTF-8');
	
		$this->y = (int)((int)($this->py) -60); 
	}




    /**
     * Parse item description
     *
     * @param  Varien_Object $item
     * @return array
     */
    protected function _parseItemDescription($item)
    {
        $matches = array();
        $description = $item->getDescription();
        if (preg_match_all('/<li.*?>(.*?)<\/li>/i', $description, $matches)) {
            return $matches[1];
        }

        return array($description);
    }

    /**
     * Before getPdf processing
     */
    protected function _beforeGetPdf() {
        $translate = Mage::getSingleton('core/translate');
        /* @var $translate Mage_Core_Model_Translate */
        $translate->setTranslateInline(false);
    }

    /**
     * After getPdf processing
     */
    protected function _afterGetPdf() {
        $translate = Mage::getSingleton('core/translate');
        /* @var $translate Mage_Core_Model_Translate */
        $translate->setTranslateInline(true);
    }

    /**
     * Format option value process
     *
     * @param  array|string $value
     * @param  Mage_Sales_Model_Order $order
     * @return string
     */
    protected function _formatOptionValue($value, $order)
    {
        $resultValue = '';
        if (is_array($value)) {
            if (isset($value['qty'])) {
                $resultValue .= sprintf('%d', $value['qty']) . ' x ';
            }

            $resultValue .= $value['title'];

            if (isset($value['price'])) {
                $resultValue .= " " . $order->formatPrice($value['price']);
            }
            return  $resultValue;
        } else {
            return $value;
        }
    }

    /**
     * Initialize renderer process
     *
     * @param string $type
     */
    protected function _initRenderer($type)
    {
        $node = Mage::getConfig()->getNode('global/pdf/' . $type);
        foreach ($node->children() as $renderer) {
            $this->_renderers[$renderer->getName()] = array(
                'model'     => (string)$renderer,
                'renderer'  => null
            );
        }
    }

    /**
     * Retrieve renderer model
     *
     * @param  string $type
     * @throws Mage_Core_Exception
     * @return Mage_Sales_Model_Order_Pdf_Items_Abstract
     */
    protected function _getRenderer($type)
    {
        if (!isset($this->_renderers[$type])) {
            $type = 'default';
        }

        if (!isset($this->_renderers[$type])) {
            Mage::throwException(Mage::helper('sales')->__('Invalid renderer model'));
        }

        if (is_null($this->_renderers[$type]['renderer'])) {
            $this->_renderers[$type]['renderer'] = Mage::getSingleton($this->_renderers[$type]['model']);
        }

        return $this->_renderers[$type]['renderer'];
    }

    /**
     * Public method of protected @see _getRenderer()
     *
     * Retrieve renderer model
     *
     * @param  string $type
     * @return Mage_Sales_Model_Order_Pdf_Items_Abstract
     */
    public function getRenderer($type)
    {
        return $this->_getRenderer($type);
    }

    /**
     * Draw Item process
     *
     * @param  Varien_Object $item
     * @param  Zend_Pdf_Page $page
     * @param  Mage_Sales_Model_Order $order
     * @return Zend_Pdf_Page
     */
    protected function _drawItem(Varien_Object $item, Zend_Pdf_Page $page, Mage_Sales_Model_Order $order)
    {
        $type = $item->getOrderItem()->getProductType();
        $renderer = $this->_getRenderer($type);
        $renderer->setOrder($order);
        $renderer->setItem($item);
        $renderer->setPdf($this);
        $renderer->setPage($page);
        $renderer->setRenderedModel($this);
        $renderer->draw();

        return $renderer->getPage();
    }

    /**
     * Set font as regular
     *
     * @param  Zend_Pdf_Page $object
     * @param  int $size
     * @return Zend_Pdf_Resource_Font
     */
    protected function _setFontRegular($object, $size = 7)
    {
        // $font = Zend_Pdf_Font::fontWithPath(Mage::getBaseDir() . '/lib/LinLibertineFont/LinLibertine_Re-4.4.1.ttf');
        $font = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA);
	$object->setFont($font, $size);
        return $font;
    }

    /**
     * Set font as bold
     *
     * @param  Zend_Pdf_Page $object
     * @param  int $size
     * @return Zend_Pdf_Resource_Font
     */
    protected function _setFontBold($object, $size = 7)
    {
        //$font = Zend_Pdf_Font::fontWithPath(Mage::getBaseDir() . '/lib/LinLibertineFont/LinLibertine_Bd-2.8.1.ttf');
        $font = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA);
	$object->setFont($font, $size);
        return $font;
    }

    /**
     * Set font as italic
     *
     * @param  Zend_Pdf_Page $object
     * @param  int $size
     * @return Zend_Pdf_Resource_Font
     */
    protected function _setFontItalic($object, $size = 7)
    {
        // $font = Zend_Pdf_Font::fontWithPath(Mage::getBaseDir() . '/lib/LinLibertineFont/LinLibertine_It-2.8.2.ttf');
        $font = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA);
	$object->setFont($font, $size);
        return $font;
    }

    /**
     * Set PDF object
     *
     * @param  Zend_Pdf $pdf
     * @return Mage_Sales_Model_Order_Pdf_Abstract
     */
    protected function _setPdf(Zend_Pdf $pdf)
    {
        $this->_pdf = $pdf;
        return $this;
    }

    /**
     * Retrieve PDF object
     *
     * @throws Mage_Core_Exception
     * @return Zend_Pdf
     */
    protected function _getPdf()
    {
        if (!$this->_pdf instanceof Zend_Pdf) {
            Mage::throwException(Mage::helper('sales')->__('Please define PDF object before using.'));
        }

        return $this->_pdf;
    }

    /**
     * Create new page and assign to PDF object
     *
     * @param  array $settings
     * @return Zend_Pdf_Page
     */
    public function newPage(array $settings = array())
    {
        $pageSize = !empty($settings['page_size']) ? $settings['page_size'] : Zend_Pdf_Page::SIZE_A4;
        $page = $this->_getPdf()->newPage($pageSize);
        $this->_getPdf()->pages[] = $page;
        $this->y = 800;

        return $page;
    }

    /**
     * Draw lines
     *
     * draw items array format:
     * lines        array;array of line blocks (required)
     * shift        int; full line height (optional)
     * height       int;line spacing (default 10)
     *
     * line block has line columns array
     *
     * column array format
     * text         string|array; draw text (required)
     * feed         int; x position (required)
     * font         string; font style, optional: bold, italic, regular
     * font_file    string; path to font file (optional for use your custom font)
     * font_size    int; font size (default 7)
     * align        string; text align (also see feed parametr), optional left, right
     * height       int;line spacing (default 10)
     *
     * @param  Zend_Pdf_Page $page
     * @param  array $draw
     * @param  array $pageSettings
     * @throws Mage_Core_Exception
     * @return Zend_Pdf_Page
     */
    public function drawLineBlocks(Zend_Pdf_Page $page, array $draw, array $pageSettings = array())
    {
        foreach ($draw as $itemsProp) {
            if (!isset($itemsProp['lines']) || !is_array($itemsProp['lines'])) {
                Mage::throwException(Mage::helper('sales')->__('Invalid draw line data. Please define "lines" array.'));
            }
            $lines  = $itemsProp['lines'];
            $height = isset($itemsProp['height']) ? $itemsProp['height'] : 10;

            if (empty($itemsProp['shift'])) {
                $shift = 0;
                foreach ($lines as $line) {
                    $maxHeight = 0;
                    foreach ($line as $column) {
                        $lineSpacing = !empty($column['height']) ? $column['height'] : $height;
                        if (!is_array($column['text'])) {
                            $column['text'] = array($column['text']);
                        }
                        $top = 0;
                        foreach ($column['text'] as $part) {
                            $top += $lineSpacing;
                        }

                        $maxHeight = $top > $maxHeight ? $top : $maxHeight;
                    }
                    $shift += $maxHeight;
                }
                $itemsProp['shift'] = $shift;
            }

            if ($this->y - $itemsProp['shift'] < 15) {
                $page = $this->newPage($pageSettings);
            }

            foreach ($lines as $line) {
                $maxHeight = 0;
                foreach ($line as $column) {

			
			
			// type image 
			if(isset($column["type"])){if("image" === $column["type"]){
                		try{ $image = Zend_Pdf_Image::imageWithPath($column["file"]); } catch(Exception $e){}	
				if(null === $image){} else{
					$mw = 50;
					$mh = 50;
					$ratio = $image->getPixelWidth() /$mw;
					$w = $image->getPixelWidth() /$ratio;
					$h = $image->getPixelHeight() /$ratio;
					if($h > $mh){
						$ratio = $image->getPixelHeight() /$mh;
						$w = $image->getPixelWidth() /$ratio;
						$h = $image->getPixelHeight() /$ratio;
					}	
					$x1 = $column["feed"];
					$y1 = $this->y -$h +20;
					$x2 = $x1 +$w;
					$y2 = $y1 +$h;
					$page->drawImage($image, $x1, $y1, $x2, $y2);
				}	
			}}





			// type checker
			if(isset($column["type"])){if("check" === $column["type"]){
        			$page->setLineColor(new Zend_Pdf_Color_GrayScale(0.2));
				$page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
        			
				// checker one
				$x1 = $column["feed"];
				$y1 = $this->y +25;
				$x2 = $column["feed"] +48;
				$y2 = $this->y -48 +25;				
				$page->drawRectangle($x1, $y1, $x2, $y2);
				// 
				
				// checker two
        			/*
				$x1 = $column["feed"] +50;
				$y1 = $this->y -32;
				$x2 = $column["feed"] +10 +50;
				$y2 = $this->y +10 -32;				
				$page->drawRectangle($x1, $y1, $x2, $y2);
			
        			$page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
				$page->drawText("Für den erneuten Versand der Ware bitte ankreuzen:", 380, $y2 -6, 'UTF-8');
				*/
				// 
				$page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
			}}





			$fontSize = empty($column['font_size']) ? 7 : $column['font_size'];
                    
			

			if (!empty($column['font_file'])) {
                        	$font = Zend_Pdf_Font::fontWithPath($column['font_file']);
                        	$page->setFont($font, $fontSize);
                    	} else {
                        	$fontStyle = empty($column['font']) ? 'regular' : $column['font'];
                        	switch ($fontStyle) {
                           	case 'bold':
                                	$font = $this->_setFontBold($page, $fontSize);
                                	break;
                            case 'italic':
                                $font = $this->_setFontItalic($page, $fontSize);
                                break;
                            default:
                                $font = $this->_setFontRegular($page, $fontSize);
                                break;
                        }
                    }

                    if (!is_array($column['text'])) {
                        $column['text'] = array($column['text']);
                    }

                    $lineSpacing = !empty($column['height']) ? $column['height'] : $height;
                    $top = 0;
			$antitop = 0;
                    foreach ($column['text'] as $part) {
                        if ($this->y - $lineSpacing < 15) {
                            $page = $this->newPage($pageSettings);
                        }

                        $feed = $column['feed'];
                        $textAlign = empty($column['align']) ? 'left' : $column['align'];
                        $width = empty($column['width']) ? 0 : $column['width'];
                        switch ($textAlign) {
                            case 'right':
                                if ($width) {
                                    $feed = $this->getAlignRight($part, $feed, $width, $font, $fontSize);
                                }
                                else {
                                    $feed = $feed - $this->widthForStringUsingFontSize($part, $font, $fontSize);
                                }
                                break;
                            case 'center':
                                if ($width) {
                                    $feed = $this->getAlignCenter($part, $feed, $width, $font, $fontSize);
                                }
                                break;
                        }
                        
			$page->drawText($part, $feed, $this->y-$antitop, 'UTF-8');
                        $antitop += 12;
                    }
                    $top += $lineSpacing;
		
                    $maxHeight = $top > $maxHeight ? $top : $maxHeight;
                }
                $this->y -= $maxHeight;
		}
        }
        return $page;
    }

	public function insertFooter($page, $source)
	{
		if ($this->y -70 < 15) {
			$page = $this->newPage($pageSettings);
			$this->_setFontRegular($page, 7);
        		$page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
		}

		$y = 70;
		
		$page->drawText(Mage::helper('sales')->__("MyGassi GmbH"), 35, $y, 'UTF-8');
		$page->drawText(Mage::helper('sales')->__("Nymphenburgerstr. 86"), 35, $y -10, 'UTF-8');
		$page->drawText(Mage::helper('sales')->__("D-80636 München"), 35, $y -20, 'UTF-8');
		$page->drawText(Mage::helper('sales')->__("support@mygassi.com"), 35, $y -30, 'UTF-8');
		$page->drawText(Mage::helper('sales')->__("www.mygassi.com"), 35, $y -40, 'UTF-8');

		$page->drawText(Mage::helper('sales')->__("Sitz der Gesellschaft ist München"), 130, $y, 'UTF-8');
		$page->drawText(Mage::helper('sales')->__("HRB 204049"), 130, $y -10, 'UTF-8');
		$page->drawText(Mage::helper('sales')->__("Ust-IdNr: DE288496229"), 130, $y -20, 'UTF-8');
		$page->drawText(Mage::helper('sales')->__("IBAN: DE60 700202700 15233881"), 130, $y -30, 'UTF-8');
		$page->drawText(Mage::helper('sales')->__("SWIFT (BIC): HYVEDEMMXXX"), 130, $y -40, 'UTF-8');

		$page->drawText(Mage::helper('sales')->__("Geschäftsführung: Sven Claar, Alexander Holzreiter"), 258, $y, 'UTF-8');
		$page->drawText(Mage::helper('sales')->__("Bankverbindung: Hypovereinsbank München"), 258, $y -10, 'UTF-8');
		$page->drawText(Mage::helper('sales')->__("KTO: 15233881"), 258, $y -20, 'UTF-8');
		$page->drawText(Mage::helper('sales')->__("BLZ: 70020270"), 258, $y -30, 'UTF-8');

	}
}
