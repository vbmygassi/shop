<?php

class Codex_Categoryimport_Model_Import
{

    protected $_verbose = false;

    protected function _errorMsg( $msg )
    {
        echo $msg."\n";
    }

    protected function _verboseMsg( $msg )
    {
        if ( $this->_verbose ) {
            echo $msg."\n";
        }
    }

    protected function _getTierarten()
    {
        $client = Mage::getModel('codex_api/api');

        // $getParams = array('merkmal' => '137' );
        $getParams = array('merkmal'=>'137','mygassionly'=>'1');
        // $getParams = array('merkmal'=>'42','mygassionly'=>'1');
        // $response = $client->call('merkmal/', 'GET', $getParams, $this->_verbose );
var_dump($getParams);
        // $response = $client->call('merkmal/?merkmal=42', 'GET', $getParams, $this->_verbose );
        $response = $client->call('merkmal/1', 'GET', $getParams, $this->_verbose );
        // $response = $client->call('merkmal/?mygassionly=1', 'GET', $getParams, $this->_verbose );
var_dump($response);
        $response = current($response);
        return $response;
    }

    /**
     * @param array $menu
     * @param null  $dom
     *
     * @return Codex_Shell_Categoryimport
     */
    protected function _createOrUpdateCategory($kat_id, $kat_sort, $kat_parent_id, $kat_store, $kat_name, Array $kat_data)
    {
        if ( $kat_parent_id == '' ) {
            $kat_parent_id = 'ROOT';
        }

        $this->_verboseMsg( "$kat_id -- $kat_name -- $kat_sort -- $kat_parent_id" );


        $category = Mage::getModel('catalog/category');
        /* @var $category Mage_Catalog_Model_Category */

        // Den Path der Root-Kategorie übernehmen
        $root_cal_col = $category->getCollection()
            ->addAttributeToFilter('erp_id', $kat_parent_id)
            ->addAttributeToSelect('path_id');
        if ($root_cat = $root_cal_col->getFirstItem()) {
            $path = explode('/', $root_cat->getPath());
            // $path = $root_cat->getPath();
        } else {
            $this->_errorMsg("$kat_parent_id : Oberkategorie konnte nicht ermittelt werden");
            return $this;
        }

        // Kategorie anlegen oder laden
        $category = Mage::getModel('catalog/category');
        $category->setStoreId( $kat_store );

        /* @var $category Mage_Catalog_Model_Category */
        if ($category_id = $category->getCollection()->addAttributeToFilter('erp_id', $kat_id)->getFirstItem()->getId()) {
            $category->load($category_id);
            $path[] = $category->getId();
            $this->_verboseMsg("..geladen über ERP-ID");
        } else {
            $category->setData($this->_getCategoryRecord());
            $category->setErpId($kat_id);

            $this->_verboseMsg("..neue Kategorie");
        }

//array_unshift($path, 1);
$path[0] = 1;
var_dump($path);

        $this->_verboseMsg('.. Path:' . join('/', $path));

        $category->setName($kat_name);
        $category->setPath(join("/",$path));
        $category->setPosition($kat_sort);
        $category->setIsActive(1);

        foreach( $kat_data AS $key => $value )
        {
            $category->setData( $key, $value );
        }

        $category->save();

        return $this;
    }

    protected function _getCategoryRecord()
    {
        $record = array(
            'name' => '',
            'path' => '',
            'description' => "",
            'meta_title' => "",
            'meta_keywords' => "",
            'meta_description' => "",
            'landing_page' => "",
            'display_mode' => "PRODUCTS",
            'is_active' => 1,
            'is_anchor' => 1,
            'url_key' => '',
            'include_in_menu' => 1,
            'image' => ''
        );
        return $record;
    }

    protected function getCategories( $parent_id = null )
    {
        $client = Mage::getModel('codex_api/api');

        // $getParams = array('parent_id' => $parent_id );
        $getParams = array('parent_id' => $parent_id,'mygassionly'=>'1','merkmal'=>'42');
        // $response = $client->call('category/', 'GET', $getParams, $this->_verbose );
var_dump($response);
var_dump($getParams);
        $response = $client->call('category/', 'GET', $getParams, $this->_verbose );
        return $response;
    }

    protected function _doImportRekursiv( $categorydata, $prefix_id )
    {
        $kat_id = $prefix_id.'.'.$categorydata['idCategory'];
        $kat_sort = $categorydata['sortNb'];
        $kat_parent_id = $prefix_id.'.'.$categorydata['parentId'];

        foreach( $categorydata['language'] AS $languagedata )
        {

            $store_id = $this->_getStoreIdByLanguageId( $languagedata['idLang'] );
            if ( $store_id )
            {
                $extradata = array(
                    'headline' => $languagedata['headline'],
                    'description' => $languagedata['text']
                );
                $name = $languagedata['title'];
                $this->_createOrUpdateCategory($kat_id,$kat_sort,$kat_parent_id,$store_id,$name, $extradata);
            }

        }

        foreach( $this->getCategories( $categorydata['idCategory'] ) AS $subcategorydata )
        {
            $this->_doImportRekursiv( $subcategorydata, $prefix_id );
        }

    }

    protected function _getStoreIdByLanguageId( $language_id )
    {
        switch( $language_id )
        {
            case 1:
            case 'de':
                return 1; // de

            case 2:
            case 'en':
                return 2; // en
        }
        $this->_errorMsg( 'language_id in '.$language_id." ist unbekannt" );
        return false;
    }

    public function doImport($verbose = false)
    {
        $this->_verbose = (bool)$verbose;

        // Zuerst einmal die Tierarten
        foreach( $this->_getTierarten() AS $id => $tierart )
        {
            $tierart_kat_id = $id.'.ROOT';
            $kat_sort = $id;
            $kat_parent_id = '';

            foreach( $tierart AS $lang_id => $kat_name )
            {
                $store_id = $this->_getStoreIdByLanguageId( $lang_id );
                if ( $store_id )
                {
                    $this->_createOrUpdateCategory($tierart_kat_id,$kat_sort,$kat_parent_id,$store_id,$kat_name, array() );
                }
            }

            foreach( $this->getCategories() AS $categorydata )
            {
                $categorydata['parentId'] = 'ROOT';
                $this->_doImportRekursiv( $categorydata, $id );
            }

        }

    }

    public function disableUnusedCategories($verbose = false)
    {
        $this->_verbose = (bool)$verbose;

        $client = Mage::getModel('codex_api/api');
        $erp_ids = $client->call('categoryids/', 'GET', array(), $this->_verbose );

        if ( $erp_ids < 5 )
        {
            $this->_errorMsg("es wurden zu wenig kategorie-ids zurückgegeben, das kann nicht sein!");
        } else {
            $categories = Mage::getModel('catalog/category')->getCollection()
                ->addAttributeToFilter('erp_id', array("nin"=>array($erp_ids)));

            foreach( $categories AS $category ) {
                if( $category->getErpId() &&  strpos( $category->getErpId(), 'ROOT') === false  ) {
                    $this->_verboseMsg('category #'.$category->getId().' (ERP-ID: '.$category->getErpId().') disabled');
                    $category->setIsActive(0)->save();
                }
            }
        }

    }
}
