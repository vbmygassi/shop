<?php

$chnk = "MyGassi-Bestellbestätigung";

if(0 === strpos($chnk, "MyGassi-Rechnung")){
	print "Ja, ist eine MyGassi-Rechnung.\n";

} 
else {
	print "Das ist keine Bestellbestätigung.\n";
}
exit(1);
