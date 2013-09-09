<?php
date_default_timezone_set("Europe/Berlin");

require_once("mygassi-config.php");
require_once(TCPDFPath);


$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('MyGassi');
$pdf->SetTitle('MyGassi');
$pdf->SetSubject('MyGassi Rechnung');
$pdf->SetKeywords('MyGassi Rechnung');
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
$pdf->setHeaderData('',0,'','',array(0,0,0), array(255,255,255));  
$pdf->setFooterData('',0,'','',array(0,0,0), array(255,255,255));  
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
$pdf->setFontSubsetting(true);
$pdf->SetFont('dejavusans', '', 14, '', true);
$pdf->AddPage();










/*
$temp = "";
foreach($invoices as $invoice){
	foreach($invoice->getAllItems() as $item){
		$temp .= $item->getName();
		$temp .= "<br/>";
	}
}
*/

$path = "shell/pdf/test.pdf";

$model = new Model();
$model->title = "Diese Rechnung wird von einer HTML/CSS Stylevorlage generiert und das freut uns alle sehr weil wir die Desighvorlagen übernehmen können.";
// $model->test  = $temp;





require_once(PDFTemplatePath . "test.php");
$pdf->writeHTMLCell(0, 0, '', '', $buff, 0, 1, 0, true, '', true);




$pdf->Output($path, 'F');
// $pdf->Output($path, 'I');




class Model{
	var $title = "MyGassi Rechung";
	var $test  = "Some Test Value";
}



exit(1);
