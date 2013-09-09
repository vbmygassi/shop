<?php
require_once("mygassi-config.php");
require_once("mygassi-logger.php");
require_once(mageroot);
Mage::app();
logger("Starting: mygassi-payone-cron");
$schedule = new Mage_Cron_Model_Schedule();
$worker = new Payone_Core_Model_Cronjob_TransactionStatus_Worker($schedule);
$worker->execute();
logger("Done: mygassi-payone-cron");
exit(1);
