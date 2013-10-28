<?php
/*
    a wild wild wild attempt to store price values in their "cent" representation
	to avoid the "EUR 10.00" + "EUR 10,12" p r o b l e m



 						*/


function centsToPrice($cents, $decim, $curry)
{
	$res = 0; 
	$dollars = $cents /100;
	$dollars = round($dollars, 2);
	$price = array("0", "00");
	$temp = "" +$dollars;
	if(false !== strpos($temp, ".")){
		$kaki = explode(".", $temp);
		$price[0] = $kaki[0];
		if(strlen($kaki[1]) == 1){
			$price[1] = $kaki[1] . "0";
		}
		else if(strlen($kaki[1]) == 2){
			$price[1] = $kaki[1];
		}
	} 
	else {
		$price[0] = $temp;
		$price[1] = "00";
	}
	$res = join($decim, $price); 
	$res = $res . " " . "€";
	return $res;
}

function testCentsToPrice($cents)
{
	$cents = round($cents, 0);
	// settings
	$decim = ",";
	$curry = "€";
	// 
	print centsToPrice($cents, $decim, $curry);
	print "\n";
}

testCentsToPrice(40 +2000);
testCentsToPrice(20 +12);
testCentsToPrice(12 +3000);
testCentsToPrice(12 +10);
testCentsToPrice(12.1 +10.11);
testCentsToPrice(12.1 +10.9999888811);
testCentsToPrice(12.1 +10.49);
testCentsToPrice(10000 +20000);
testCentsToPrice(1 +2);
testCentsToPrice(1 +2 +0);
testCentsToPrice(11 +24);



