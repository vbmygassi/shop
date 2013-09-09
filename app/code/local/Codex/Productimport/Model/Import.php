<?php

class Codex_Productimport_Model_Import
{
    public function importSingleProduct( $product, $verbose = false )
    {
        $row = $this->_getProductRow( $product, $verbose );

        Mage::getSingleton( 'fastsimpleimport/import' )
            ->processProductImport(  $row );

        echo "Done.\n";
    }

    public function importProducts( $limit, $mygassionly = false, $verbose = false )
    {
        $this->_importMultiselectValues($verbose);

        $articleCount = $this->_getProductCount( $verbose, $mygassionly );
        $currentOffset = 0;

        while ( $currentOffset < $articleCount ) {
            echo "Importing $limit products, starting at offset $currentOffset...\n";

            $rows = $this->_getProductRows( $limit, $currentOffset, $verbose, $mygassionly );

            Mage::getSingleton( 'fastsimpleimport/import' )
                ->processProductImport( $rows );

            $currentOffset += $limit;
        }

        echo "Done.\n";

        return $articleCount;
    }

    public function dumpProducts( $limit, $verbose = false, $mygassionly = false , $product = false)
    {
        if ($product) {
            var_dump( $this->_getProductRow( $product, $verbose ) );
        } else {
            var_dump( $this->_getProductRows( $limit, 0, $verbose, $mygassionly ) );
        }
    }

    protected function _getProductRows( $limit, $offset, $verbose = false, $mygassionly = false )
    {
        $client = $this->getApi();

        $getParams = array( 'limit' => $limit, 'offset' => $offset, 'mygassionly' => (int)$mygassionly );
        $response = $client->call( 'article/', 'GET', $getParams, $verbose );

        return $response;
    }

    protected function _getProductRow( $product, $verbose = false )
    {
        $client = $this->getApi();

        $getParams = array( 'product' => $product );
        $response = $client->call( 'article/', 'GET', $getParams, $verbose );

        return $response;
    }

    protected function _getProductCount( $verbose = false, $mygassionly = false )
    {
        $client = $this->getApi();

        $getParams = array( 'mygassionly' => (int)$mygassionly );
        $response = $client->call( 'articlecount', 'GET', $getParams, $verbose );
        if ( $response && isset( $response['count'] ) ) {
            return $response['count'];
        }

        return 0;
    }

    protected function _importMultiselectValues( $verbose = false )
    {
        $importer = Mage::getModel( 'codex_multiselectimport/import' );
        $importer->doImport( $verbose );
    }


    /**
     * Api Model.
     *
     * @return Codex_Api_Model_Api
     */
    protected function getApi()
    {
        return Mage::getModel( 'codex_api/api' );
    }
}
