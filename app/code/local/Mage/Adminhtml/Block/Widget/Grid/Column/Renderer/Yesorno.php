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
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml grid item renderer
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Yesorno 
	extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
	{
	
	public function _getValue(Varien_Object $row)
	{
		$defaultValue = $this->getColumn()->getDefault();
		$data = parent::_getValue($row);
		switch($data){
			default:
			case null: 
			case 0: 
			case "0":
				$data = __("0: Nein");
				break;
			case 1:
			case "1":
			case "Ja":
				$data = __("1: Ja");
				break;
		}
		$res = is_null($data) ? $defaultValue : $data;
		return $this->escapeHtml($res);
	}

}
