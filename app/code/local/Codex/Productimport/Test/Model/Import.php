<?php

error_reporting(0);

class Codex_Productimport_Test_Model_Import extends EcomDev_PHPUnit_Test_Case
{

    public function testSingleProductImport()
    {
        $testProduct = $this->getTestProductData();

        /** @var Mage_Catalog_Model_Product $product */
        $product = $this->importProductBySku($testProduct['sku']);

        $this->assertNotNull($product);

        foreach ($testProduct as $key => $value)
        {
            if ($product->getData($key) != $value)
            {
                $errorMessage = 'Failed to correctly import SKU field ' . $key . '=' . $value;
                $this->fail($errorMessage);
            }
        }
    }


    public function testProductImportMyGassi()
    {
        $this->_testSingleProductImportBySku('01841');
        $this->_testSingleProductImportBySku('01842');
        $this->_testSingleProductImportBySku('06091');
        $this->_testSingleProductImportBySku('06144');
        $this->_testSingleProductImportBySku('06191');
        $this->_testSingleProductImportBySku('09307');
        $this->_testSingleProductImportBySku('09309');
        $this->_testSingleProductImportBySku('09308');
        $this->_testSingleProductImportBySku('09316');
        $this->_testSingleProductImportBySku(24641);
        $this->_testSingleProductImportBySku(25115);
        $this->_testSingleProductImportBySku(25117);
        $this->_testSingleProductImportBySku(65434);
        $this->_testSingleProductImportBySku(68414);
        $this->_testSingleProductImportBySku(75795);
        $this->_testSingleProductImportBySku(31381);
        $this->_testSingleProductImportBySku(65426);
        // 130186

        $this->_testSingleProductImportBySku(25118);
    }


    /**
     * Test single product.
     *
     * @param $sku
     *
     * @return bool
     */
    public function _testSingleProductImportBySku($sku){
        /** @var Mage_Catalog_Model_Product $product */
        $product = $this->importProductBySku($sku);

        $errorMessage = 'Failed to correctly import SKU ' . $sku;

        if (null == $product)
        {
            $this->fail($errorMessage);
            return false;
        }

        $this->assertNotNull($product, $errorMessage);
        $this->assertNotEmpty($product->getName(), $errorMessage);
        $this->assertEquals($sku, $product->getSku(), $errorMessage);

        return true;
    }


    /**
     * Run a single product import.
     *
     * @param $testProduct
     *
     * @return Mage_Catalog_Model_Product
     */
    public function importProductBySku($testProduct)
    {
        // Product löschen
        $product = $this->loadProductBySku($testProduct);
        if ($product)
        {
            $product->delete();
        }

        $this->getImportModel()->importSingleProduct($testProduct, true );

        // Laden
        $product = $this->loadProductBySku($testProduct);

        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
        $write->query("INSERT INTO catalog_product_entity_media_gallery (attribute_id, entity_id, `value`)
                        SELECT ga.attribute_id, v.entity_id, v.value
                        FROM catalog_product_entity_varchar v
                        INNER JOIN eav_entity_type et ON et.entity_type_code='catalog_product'
                        INNER JOIN eav_attribute va ON va.entity_type_id=et.entity_type_id AND va.frontend_input='media_image' AND va.attribute_id=v.attribute_id
                        INNER JOIN eav_attribute ga ON va.entity_type_id=et.entity_type_id AND ga.attribute_code='media_gallery'
                        LEFT JOIN catalog_product_entity_media_gallery g ON g.entity_id=v.entity_id AND g.value=v.value
                        WHERE v.value<>'no_selection' AND v.value<>'' AND g.value IS NULL");

        return $product;
    }


    /**
     * @return false|Codex_Productimport_Model_Import
     */
    protected function getImportModel()
    {
        return Mage::getModel('codex_productimport/import');
    }

    /**
     * @param $sku
     * @return Mage_Catalog_Model_Product
     */
    protected function loadProductBySku($sku)
    {
        /** @var $product Mage_Catalog_Model_Product */
        $productModel = Mage::getModel('catalog/product');
        return $productModel->loadByAttribute('sku', $sku);
    }

    protected function getTestProductData()
    {
        return array(
            'type_id' => 'simple',
            'sku' => 65428,
            'packaging_unit' => 48,
            'name' => 'ART SPORTIV PLUS GESCHIRR CROSS 25MM 58-70CM, SCHWARZ/SCHWARZ',
            'description' => 'Stufenlos verstellbar. Stabiles Nylonmaterial, weiche Fleecepolsterung. Verchromte Beschläge.',
            'short_description' => 'Stufenlos verstellbar. Stabiles Nylonmaterial, weiche Fleecepolsterung. Verchromte Beschläge.',
            'length' => 58,
            'width' => 25,
            'height' => 0,
            'diameter' => 0,
            'volume' => 0,
            'size' => 'M',
            'bargain' => 0,
            'new' => 0,
            'status' => 1,
            'visibility' => 4,
            'enable_googlecheckout' => 1,
            'price' => 0.0000,
            'basisartikelnr' => '130186',
            'weight' => 181,
            'options_container' => 'container2',
            'msrp_enabled' => 2,
            'msrp_display_actual_price_type' => 4,
            'delivery_time' => '2-3 Tage',
        );
    }

}
