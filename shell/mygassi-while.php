<?php
/**
 * crontab -e
 */ 

define("ExportProductCatalog", "php shell/mygassi-export-productcatalog.php");
define("Sales", "php shell/mygassi-salesexport.php");
define("Parcels", "php shell/mygassi-check-parcels.php");
define("Retoure", "php shell/mygassi-retoure.php");

while(true){
	print "Sales\n";
	exec(Sales);
	print "Parcels \n";
	exec(Parcels);
	print "Retoure \n";
	exec(Retoure);
	sleep(60 *60);
}
