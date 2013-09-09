<?php
require_once("mygassi-config.php");
require_once("mygassi-logger.php");
require_once(mageroot);
Mage::app();
logger("Starting: mygassi-manipulate-title");

$coll = Mage::getModel("catalog/product")->getCollection();
foreach($coll as $product){
	$product->load($product->getId());
	$temp = $product->getName();
	$temp = explode(",", $temp);
	if(count($temp) < 1){
		continue;
	}
	$i = 0;
	foreach($temp as $chunk){
		if(" " != substr($chunk, 0, 1)){
			$chunk = " " . $chunk;
			$temp[$i] = $chunk;
		}
		$i++;
	}
	$temp = implode(",", $temp);
	$temp = str_replace("ENTWIRRUNGSKAMM", "Entwirrungskamm", $temp);
	$temp = str_replace("ENTWIRRUNGSKAmm", "Entwirrungskamm", $temp);
	$temp = str_replace("STAUB- UND FLOHKAMM", "Staub- und Flohkamm", $temp);
	$temp = str_replace("STAUB- UND FLOHKAmm", "Staub- und Flohkamm", $temp);
	$temp = str_replace("NISSENKAMM", "Nissenkamm", $temp);
	$temp = str_replace("NISSENKAmm", "Nissenkamm", $temp);
	$temp = str_replace("UNTERWOLLKAMM", "Unterwollkamm", $temp);
	$temp = str_replace("UNTERWOLLKAmm", "Unterwollkamm", $temp);
	$temp = str_replace("PFLEGEHANDSCHUH", "Pflegehandschuh", $temp);
	$temp = str_replace("FLOHFÄNGER", "Flohfänger", $temp);
	$temp = str_replace("ART SPORTIV", "Art Sportiv", $temp);
	$temp = str_replace("LEINE", "Leine", $temp);
	$temp = str_replace("ROT", "rot", $temp);
	$temp = str_replace("SCHWARZ", "schwarz", $temp);
	$temp = str_replace("HALSBAND", "Halsband", $temp);
	$temp = str_replace("VERSTELLBAR", "verstellbar", $temp);
	$temp = str_replace("PLUS", "plus", $temp);
	$temp = str_replace("KOPPEL", "Koppel", $temp);
	$temp = str_replace("LIEGEDECKE", "Liegedecke", $temp);
	$temp = str_replace("COSY", "'Cozy'", $temp);
	$temp = str_replace("BEIGE", "beige", $temp);
	$temp = str_replace("QUEENY TEDDY", "'Queeny Teddy'", $temp);
	$temp = str_replace("SUPER", "Super", $temp);
	$temp = str_replace("DELUXE", "Deluxe", $temp);
	$temp = str_replace("BED", "Bed", $temp);
	$temp = str_replace("GESTEPPT", "gesteppt", $temp);
	$temp = str_replace("MEDIFLEECE HEIMTIERDECKE", "Medifleece Heimtierdecke", $temp);
	$temp = str_replace("CAR-GUARD", "Car-Guard", $temp);
	$temp = str_replace("GRAU", "grau", $temp);
	$temp = str_replace("AUTOSCHUTZDECKE", "Autoschutzdecke", $temp);
	$temp = str_replace("ABSTANDHALTER", "Abstandhalter", $temp);
	$temp = str_replace("KOFFERRAUMBELÜFTUNG", "Kofferraumbelüftung", $temp);
	$temp = str_replace("VERBINDUNGSSTÜCK", "Verbindungsstück", $temp);
	$temp = str_replace("FÜR", "für", $temp);
	$temp = str_replace("SICHERHEITSGESCHIRR", "Sicherheitsgeschirr", $temp);
	$temp = str_replace("AUTOSICHERHEITSGESCHIRR", "Autosicherheitsgeschirr", $temp);
	$temp = str_replace("AUTOSCHUTZGITTER", "Autoschutzgitter", $temp);
	$temp = str_replace("CHROM", "chrom", $temp);
	$temp = str_replace("GRÖßE", "grösse", $temp);
	$temp = str_replace("PROFIKRALLENSCHEERE", "Profikrallenschäre", $temp);
	$temp = str_replace("PERFECT CARE", "Perfect Care", $temp);
	$temp = str_replace("FELLSCHERE", "Fellschere", $temp);
	$temp = str_replace("MM", "mm", $temp);
	$temp = str_replace("CM", "cm", $temp);
	$product->setName($temp);
	$product->save();	
}
logger("Done: mygassi-manipulate-title");

exit(1);
