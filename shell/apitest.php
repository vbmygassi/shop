<?php

require_once 'abstract.php';

class Mage_Shell_Apitest extends Mage_Shell_Abstract
{

    public function run()
    {
        if ($this->getArg('test')) {
            $client = Mage::getModel('codex_api/api'); 

            // existing url
            $response = $client->call('order/696576');
            echo count( $response ) . "\n";

            // bogus url
            try {
                $error =  $client->call('mike/ist/der/beste/1337');
            } catch (Exception $e) {
                echo $e->getMessage() . "##" . $e->getCode() . "\n";
            }

            // post something
            //$response = $client->call('order','POST', array('id' => 1));

            // update entitites
            //$response = $client->call('order/1','PUT', array('id' => 1));

            // delete
            //$response = $client->call('order/1','DELETE');
        } else {
            echo $this->usageHelp();
        }
    }

    /**
     * Retrieve Usage Help Message
     *
     */
    public function usageHelp()
    {
        return <<<USAGE
Usage:  php -f apitest.php -- [options]

  --test              Test API

USAGE;
    }
}

$shell = new Mage_Shell_Apitest();
$shell->run();
