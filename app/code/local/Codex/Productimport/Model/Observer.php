<?php

class Codex_Productimport_Model_Observer
{

    public function avs_fastsimpleimport_entity_product_init_categories( $event )
    {
        /** @var $transport Varien_Object */
        $transport = $event->getTransport();

        $categories = array();

        $collection = Mage::getResourceModel('catalog/category_collection');
        /* @var $collection Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Collection */

        $collection->joinAttribute('erp_id', 'catalog_category/erp_id', 'entity_id', null, 'left');

        foreach ($collection as $category) {

            // Mapping über ERP-Id
            $categories[ $category->getData('erp_id') ] = $category->getId();

        }

        $transport->setCategories( $categories );

        return $this;

    }

}