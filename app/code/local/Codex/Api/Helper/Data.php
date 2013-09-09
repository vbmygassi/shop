<?php
/**
 * Class Codex_Catalog_Helper_Data
 *
 */

class Codex_Api_Helper_Data extends Mage_Core_Helper_Abstract
{

    protected static function _getConfig( $node )
    {
        return (string) Mage::getConfig()->getNode($node);
    }

    public static function getCatalogProductSchwellwert()
    {
        return self::_getConfig('codex/catalog/product/threshold');
    }
}