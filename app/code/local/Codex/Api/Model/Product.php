<?php
/**
 * Class Product
 *
 */

/**
 * Class Product
 */
class Codex_Api_Model_Product extends Mage_Catalog_Model_Product
{
    const AMPEL_ROT = 'rot';

    const AMPEL_GELB = 'gelb';

    const AMPEL_GRUEN = 'gruen';

    /**
     * @return Codex_Api_Model_Api|false
     */
    public function getApi()
    {
        static $api;

        if ( null == $api )
        {
            $api = Mage::getModel('codex_api/api');
        }

        return $api;
    }

    public function getFreibestand()
    {
        static $id = null;

        if ( $id != $this->getSku() )
        {
            $repsonse = $this->getApi()->call('article/' . urlencode(parent::getData('sku')));

            if ( !isset($repsonse['qty']) )
            {
                $repsonse['qty'] = 0;
            }

            // write data to current product
            $this->setData('qty', $repsonse['qty']);
        }

        return $this->getData('qty');
    }

    public function getAmpel()
    {
        /* @var $session Mage_Customer_Model_Session */
        $session = Mage::getSingleton('customer/session');

        $mindestabnahme = 0;
        if ( !$session->isLoggedIn() )
        {
            return false;
        }

        $mindestabnahme = $this->getMinQty();

        if ( $this->getFreibestand() < $mindestabnahme )
        {
            return self::AMPEL_ROT;
        }

        /* @var $helper Codex_Api_Helper_Data */
        $helper = Mage::helper('codex_api');

        if ( $mindestabnahme == 0 )
        {
            return self::AMPEL_GRUEN;
        }

        $division = $this->getFreibestand() / $mindestabnahme;

        if ( $helper->getCatalogProductSchwellwert() >= $division )
        {
            return self::AMPEL_GELB;
        }

        if ( $helper->getCatalogProductSchwellwert() < $division )
        {
            return self::AMPEL_GRUEN;
        }

        return self::AMPEL_ROT;
    }
}
