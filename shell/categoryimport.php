<?php

require_once 'abstract.php';

class Codex_Shell_Categoryimport extends Mage_Shell_Abstract
{
    public function run()
    {
        // prepare
        Mage::app()->setCurrentStore( Mage_Core_Model_App::ADMIN_STORE_ID );

        $importer = Mage::getModel( 'codex_categoryimport/import' );

        // check what we're up to
        if ( $this->getArg( 'import' ) ) {
            $importer->doImport( $this->getArg( 'verbose' ) );

        } else if ( $this->getArg( 'disable' ) ) {
            $importer->disableUnusedCategories( $this->getArg( 'verbose' ) );

        } else {
            $this->usageHelp();
        }
    }

    /**
     * usage infos
     *
     * @return string|void
     */
    public function usageHelp()
    {
        echo <<<USAGE
Usage:  php -f categoryimport.php -- --<action> [--verbose]

possible action:
    --import (process new/updated categories)

    --disable (disable categories not mentioned in source)

optional parameters:
    --verbose (be more verbose)

USAGE;

        exit(1);
    }
}

$categoryimport = new Codex_Shell_Categoryimport();
$categoryimport->run();