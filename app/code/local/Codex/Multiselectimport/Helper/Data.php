<?php

class Codex_Multiselectimport_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getLanguageToStore()
    {
        $data = array();

        foreach (Mage::app()->getStores() AS $store) {
            $data[$store->getCode()] = array('id' => $store->getId(), 'code' => $store->getCode());
        }

        return $data;
    }
}
