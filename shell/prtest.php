<?php
print "Eingabe: lustig: (19.99): ";
$handle = fopen("php://stdin", "r");
$price = fgets($handle);
$price = (float)$price;
print ">" . $price . "\n";
$price = round($price, 2);
print ">" . $price . "\n";
$price *= 100;
print ">" . $price . "\n";
$price = (int)$price;
print ">" . $price . "\n";
exit(1);
