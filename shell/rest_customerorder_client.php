<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gerdtvla
 * Name: Vladimir Gerdt
 * E-Mail: vladimir.gerdt@karlie.de
 * Create date: 29.04.2013 08:40
 * Update date: 27.05.2013 15:25
 * Version: 1.1
 * To change this template use File | Settings | File Templates.
 */

// Auftrag mit Positionen im Array hinterlegen
$kvg_arr_order = array();

$kvg_lfdnr = 12;                                                                // Eindeutige Auftragsnummer


/////// Kopfdaten
$kvg_arr_order[$kvg_lfdnr]['key'] = $kvg_lfdnr;                                 // Eindeutige Auftragsnummer
$kvg_arr_order[$kvg_lfdnr]['order_number'] = '123';                             // Bestellnummer, optional
$kvg_arr_order[$kvg_lfdnr]['customer_note'] = 'test comment';                   // Kommentar zum Auftrag max. 200 Zeichen, optional
$kvg_arr_order[$kvg_lfdnr]['order_date'] = '2013-04-24';                        // Auftragsdatum
$kvg_arr_order[$kvg_lfdnr]['order_time'] = '08:13';                             // Auftragszeit

/////// Kundendaten
$kvg_arr_order[$kvg_lfdnr]['customer_first_name'] = 'Vladimir';                 // Vorname
$kvg_arr_order[$kvg_lfdnr]['customer_last_name'] = 'Gerdt';                     // Nachname
$kvg_arr_order[$kvg_lfdnr]['customer_county'] = 'Deutschland';                  // Land
$kvg_arr_order[$kvg_lfdnr]['customer_city'] = 'Bad Wünnenberg Haaren';          // Stadt
$kvg_arr_order[$kvg_lfdnr]['customer_postal_code'] = '33181';                   // Postleitzahl
$kvg_arr_order[$kvg_lfdnr]['customer_street'] = 'Graf-Zeppelin-Strasse 13';     // Strasse
$kvg_arr_order[$kvg_lfdnr]['customer_phone'] = '+49 (2957) 77-100';             // Telefon
$kvg_arr_order[$kvg_lfdnr]['customer_mobile'] = '';                             // Mobile Telefon
$kvg_arr_order[$kvg_lfdnr]['customer_fax'] = '+49 (2957) 77-399';               // Fax
$kvg_arr_order[$kvg_lfdnr]['customer_email'] = 'info@karlie.de';                // Email

/////// Positionsdaten
// Artikel 1
$kvg_arr_order[$kvg_lfdnr]['position']['43024']['article_number'] = '43024';    // Artikelnummer
$kvg_arr_order[$kvg_lfdnr]['position']['43024']['amount'] = 3;                  // Menge
$kvg_arr_order[$kvg_lfdnr]['position']['43024']['price'] = 10.11;               // Preis
// Artikel 2
$kvg_arr_order[$kvg_lfdnr]['position']['43025']['article_number'] = '43025';    // Artikelnummer
$kvg_arr_order[$kvg_lfdnr]['position']['43025']['amount'] = 1;                  // Menge
$kvg_arr_order[$kvg_lfdnr]['position']['43025']['price'] = 10.11;               // Preis
// Artikel 3.... usw.


////////////// ab hier nichts aendern //////////////

// Auftragsarray als JSON Format initialisieren
$kvg_arr_invoice_json = json_encode( $kvg_arr_order );

// REST POST
// URL
$request =  'http://csdaten.karlie.de/Git/magento-project-karlie/webservice/public/index.php/v1/order';
// Auftragsarray und Applikation, welches ueber POST an die URL uebergeben wird
$postargs = 'appid=2&args=' . urlencode( $kvg_arr_invoice_json );

// headers fuer JSON setzten
$headers = array(
    'Accept: application/json',
    'Content-Type: application/json'
);

// mit curl die REST - Schnittstelle ueber POST ansprechen
$handle = curl_init();
curl_setopt($handle, CURLOPT_URL, $request);
curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);

curl_setopt($handle, CURLOPT_POST, true); // POST
curl_setopt($handle, CURLOPT_POSTFIELDS, $postargs);

$response = curl_exec($handle); // ausfuehren und Callback in $response
$code = curl_getinfo($handle, CURLINFO_HTTP_CODE); // Status Code

// close cURL resource, and free up system resources
curl_close($handle);
echo $code . '<br />';
echo $response;

?>
