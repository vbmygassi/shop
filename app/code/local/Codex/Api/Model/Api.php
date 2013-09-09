<?php

class Codex_Api_Model_Api
{
    /**
     * @var Zend_Http_Client
     */
    protected $_client;

    /**
     * Get base url from config
     *
     * @return string
     */
    protected function _getClientUrl()
    {
        return (string)Mage::getConfig()->getNode( 'codex/api/client_url' );
    }

    /**
     * Set http client
     *
     * @param \Zend_Http_Client $client
     */
    public function setClient( $client )
    {
        $this->_client = $client;
    }

    /**
     * Get http client
     *
     * @return \Zend_Http_Client
     */
    public function getClient()
    {
        if ( !$this->_client ) {
            $this->setClient(new Zend_Http_Client(null, array('timeout' => 30)));
        }

        return $this->_client;
    }

    /**
     * Call path (with optional parameters) on base url using the given method.
     *
     * @throws Zend_Http_Client_Exception
     * @param $path URL path
     * @param string $method POST|GET|PUT|DELETE
     * @param bool|array $args Optional parameters for PUT/POST requests
     * @return mixed
     */
    public function call( $path, $method = 'GET', $args = false, $verbose = false )
    {
        $client = $this->getClient();
        $client->setUri( $this->_getClientUrl() . $path );

        if ( $verbose )
        {
            echo "### URL: ".$this->_getClientUrl() . $path."\n";
        }

        if ( $args && ($method == 'POST' || $method == 'PUT') ) {
            foreach ( $args as $k => $v ) {
                $client->setParameterPost( $k, $v );
            }
        } else if ($args && $method == 'GET') {
            foreach ( $args as $k => $v ) {
                $client->setParameterGet( $k, $v );
            }
        }

        try {
            $result = $client->request( $method );

            if ($verbose) {
                echo "###Result:\n";
                var_dump($result->getBody());
                echo "###\n\n";
            } 
            if (!$result->isSuccessful()) {
                throw new Exception('API request failed miserably', $result->getStatus());
            }

            return Zend_Json_Decoder::decode($result->getBody());

        } catch (Zend_Http_Client_Exception $e) {
            throw $e;
        }
    }
}

?>
