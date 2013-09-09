<?php

class Codex_Multiselectimport_Model_Import
{
    protected $_verbose = false;

    protected function _errorMsg( $msg )
    {
        echo $msg . "\n";
    }

    protected function _verboseMsg( $msg )
    {
        if ( $this->_verbose ) {
            echo $msg . "\n";
        }
    }

    public function doImport( $verbose = false )
    {
        $this->_verbose = (bool)$verbose;

        $this->_refresh();
    }

    protected function _refresh()
    {

        $rawData = $this->_getAttributeData();

        foreach ($rawData as $attr => $values) {
            $data = array();

            foreach ($values as $id => $valueRow) {
                $data[$id] = $valueRow;
            }

            $this->_updateAttribute($attr, $data);
        }

        return $this;
    }

    protected function _getAttributeData( )
    {
        $client = $this->_getApi();
       	
	$response = $client->call( 'merkmal/', 'GET', false, $this->_verbose );

        return $response;
    }

    protected function _getApi()
    {
        return Mage::getModel( 'codex_api/api' );
    }

    protected function _updateAttribute( $code, $data )
    {
        $new_i = 0;
        $option_values = array();

        $this->_verboseMsg( 'attribute: '.$code );

        static $language2store;
        if ( !$language2store ) {
            $language2store = Mage::helper( 'codex_multiselectimport' )->getLanguageToStore();
        }

        $attribute = Mage::getSingleton( 'eav/config' )->getAttribute( 'catalog_product', $code );
        foreach ( $data AS $key => $language ) {
            $found = false;

            if ( $key != '' ) {
                if ( $attribute->usesSource() ) {
                    foreach ( $attribute->getSource()->getAllOptions( false ) AS $opt_id => $option ) {
                        if ( $key == $option[ 'label' ] ) {
                            $option_values[ 'value' ][ $option[ 'value' ] ][ 0 ] = $key;
                            $found = true;

                            foreach ( $language AS $language_key => $text ) {
                                $store_id = $language2store[ $language_key ][ 'id' ];
                                $option_values[ 'value' ][ $option[ 'value' ] ][ $store_id ] = $text;
                            }

                        }
                    }
                }
                if ( $found == false ) {
                    $option_values[ 'value' ][ 'option_' . $new_i ][ 0 ] = $key;

                    foreach ( $language AS $language_key => $text ) {
                        $store_id = $language2store[ $language_key ][ 'id' ];
                        $option_values[ 'value' ][ 'option_' . $new_i ][ $store_id ] = $text;
                    }

                    $new_i++;
                }
            }
        }

        $attribute->setOption( $option_values );
        $attribute->save();

        return $this;
    

}
}
