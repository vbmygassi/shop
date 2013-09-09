<?php

require_once 'abstract.php';

class Codex_Shell_Productimport extends Mage_Shell_Abstract
{
    public function run()
    {
        // prepare
        Mage::app()->setCurrentStore( Mage_Core_Model_App::ADMIN_STORE_ID );

        // verbose mode?
        $verbose = $this->getArg( 'verbose' );
        $mygassi = $this->getArg( 'mygassionly' );
        $limit = $this->getArg( 'limit' );
        $product = $this->getArg('product');

        if ($limit === false) {
            $limit = 100;
        }

        // check what we're up to
        if ( $this->getArg( 'import' ) ) {
            /** @var $importer Codex_Productimport_Model_Import */
            $importer = Mage::getModel('codex_productimport/import');

            if ($product) {
                $importer->importSingleProduct($product, false );
            } else {
                $importer->importProducts($limit, $mygassi, $verbose);
            }

            // clean image cache
            exec("rm -Rf ".Mage::getBaseDir('media').'/catalog/product/cache');

        } else if ( $this->getArg( 'dump' ) ) {
            /** @var $importer Codex_Productimport_Model_Import */
            $importer = Mage::getModel('codex_productimport/import');
            $importer->dumpProducts($limit, $mygassi, $verbose, $product);


        } else {
            $this->usageHelp();

        }
    }

    /**
     * Get the configured PHP memory limit from our config (we overwrite the default settings)
     *
     * @return string configured memory limit (compliant to memory_limit php option)
     */
    protected function _getMemlimit()
    {
        return (string)Mage::getConfig()->getNode( 'codex/productimport/memlimit' );
    }

    /**
     * Return configured php binary. On some systems we have to execute php5.3 instead of php
     *
     * @return string executable php binary name
     */
    protected function _getPhpBinaryName()
    {
        $binary = (string)Mage::getconfig()->getNode( 'codex/productimport/php_binary' );

        if ( !$binary || !is_file( $binary ) || !is_executable( $binary ) ) {
            return false;
        }

        return $binary;
    }

    /**
     * help!
     *
     * @return string|void
     */
    public function usageHelp()
    {
        echo <<<USAGE
Usage:  php -f productimport.php -- --<action> [--limit <x>] [--verbose]

possible action:
    --import (process new/updated products)
    --dump   (dump rows)

parameters:
    --product only import product x
    --limit  limit products to x
    --mygassionly only import mygassionly

optional parameters:
    --verbose (be more verbose)

USAGE;

        exit(1);
    }


}

$customerimport = new Codex_Shell_Productimport();
$customerimport->run();