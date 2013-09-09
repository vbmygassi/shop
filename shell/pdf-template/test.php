<?php

$buff = <<<EOD



<html>
	<head>
		<style type="text/css">
			 body  { font-family: Helvetica; font-size: 2.5mm; color: #000000; }
			 table { width: 100%; }
			 tr    { margin-botton: 20px; padding: 20px; }
			 td    { background-color: #ccddee; }
			.logo  { text-align: right; }
			.rchn  { font-size: 2.5mm; }
			.rchg  { font-size: 1.5mm; }
		</style> 
	</head>
	<body>
		<table>
			<!-- Logo -->
			<tr>
				<td colspan="2" class="logo">
					Schnuggywuggy Logo
				</td>
			</tr>

			
			<!-- Title -->
			<tr>
				<td colspan="2">
					<span class="rchn">Rechnungsnummer</span>
					<br/>
					<span class="rchg">Bestellung</span>
					<br/>
					<span class="rchg">Datum</span>
				</td>
			</tr>
			
			<!-- Verkauft an : Lieferung an -->
			<tr>
				<td>
					<table>
						<tr><td>VERKAUFT AN:</td></tr>
						<tr><td>Adresse</td></tr>
						<tr><td>Stadt</td></tr>
						<tr><td>PLZ</td></tr>
						<tr><td>Land</td></tr>
					</table>
				</td>
				<td>
					<table>
						<tr><td>VERSAND AN:</td></tr>
						<tr><td>Adresse</td></tr>
						<tr><td>Stadt</td></tr>
						<tr><td>PLZ</td></tr>
						<tr><td>Land</td></tr>
					</table>
				</td>
			</tr>

			<!-- Waren -->
			<tr>
				<td colspan="2">Waren</td>
			</tr>
			


			<!-- Warentabelle -->
			<tr>
				<td colspan="2">
					<table>
						<tr>
							<td>Anzahl</td>
							<td>Artikelbeschreibung</td>
							<td>Stückpreis</td>
							<td>Total</td>
						</tr>
					</table>
				</td>		
			</tr>
		
				
			<!-- Zahlart : Total -->
			<tr>
				<td>
					<table>
						<tr><td>Zahlart</td></tr>
					</table>
				</td>
				
				<td>
					<table>
						<tr><td>Rechnung</td></tr>
						<tr><td>Zwischensummer</td></tr>
						<tr><td>Steuer</td></tr>
						<tr><td>Versandkosten</td></tr>
						<tr><td>Total</td></tr>
					</table>
				</td>
			</tr>

		</table>
	</body>
</html>



EOD;
