<?php
require_once __DIR__ . '/src/bootstrap.php';
require_once __DIR__ . '/src/Database.php';

Logger::info("\n\n#####################################################################################");
Logger::info("----------------- Start liste.php ----------------------");
session_start();

$_REQUEST['application'] = "/kunden/liste.php";
$app = $_REQUEST['application'];
if (!isset($_SESSION["admin_logged_in"]) || $_SESSION["admin_logged_in"] !== true) {
    header("Location: /mkadmin/login.php?application=" . $app);
    exit;
} else {
	Logger::audit("Aufruf liste.php.");
}

/* Die Daten sind entsprechend der Felder der Datenbank aufgebaut.  */
/*
$mk_kunde   = ['id'=>0, 'barcode'=>'', 'kundennummer'=>'', 'tafel'=>false, 'kaufhaus'=>false, 
	'nachweis'=>'', 'gueltigkeit'=>'', 'bemerkung'=>'', 'changed'=>'', 'error'=>'' ];
$mk_person  = ['id'=>0, 'kunde_id'=>0, 'p_type'=>'', 'vorname'=>'', 'nachname'=>'', 'error'=>''];
$mk_tafel   = ['id'=>0, 'kunde_id'=>0, 'personen'=>0, 'preis'=>0, 'gruppe'=>'weiss', 'error'=>''];
$mk_adresse = ['id'=>0, 'person_id'=>0, 'strasse'=>'', 'hausnummer'=>'', 'plz'=>'', 'ort'=>'', 'error'=>''];
$mk_kontakt = [['id'=>0, 'person_id'=>0, 'k_type'=>'', 'kontakt'=>'', 'error'=>'']];

$nachweise = ['ALG','Bafög','Bürgergeld','Rente','Grundsicherung','Gehalt','Wohngeld'];
$kontakt_types = ['Email','Festnetz','Mobil'];
$action = "";
$error = "";
*/

set_error_handler(function($severity, $message, $file, $line) {
	if (!(error_reporting() & $severity)) {
		Logger::info("MAIN:: Error Handler");
		return;
	}
	throw new ErrorException($message, 0, $severity, $file, $line);
});

# -------------- Kundenliste ---------------
try {
$db = new DbKunden();
Logger::info("Main:: DB " . $db);
$kundenliste = $db->getKunden();
Logger::info("Main:: Last operation '" . $db->last() . "'");
?>
<!-- ################################################################## --> 

<!DOCTYPE html> 
<html> 
<head><link rel="stylesheet" href="html/style.css">
	<link rel="icon" type="image/png" href="/kunden/html/favicon.png">
</head> 
<body> 

<!-- ############################################################################# -->
<table class=frame width=50%><tr><td class="frame title">
<?php include "include/header.php"; ?>
</td></tr>
</table>
<table class="list">
<!-- ############################################################################# -->

<!-- ================= Kundenliste ============================= -->
<tr><th colspan=9>Kunde</th><th colspan=5>Adresse</th><th colspan=4>Tafel</th>
	<th colspan=1>Kontakt</th><th colspan=1> Kontaktperson </th></tr>
<tr><th> Id </th><th> KNr </th>
	<th> Name </th><th> Vorname </th>
	<th>T</th><th>K</th><th>Gültigkeit</th><th>Nachweis</th><th>Bemerkung</th>
	<th>Id</th><th>Strasse</th><th>Hausnr</th><th>PLZ</th><th>Ort</th>
	<th>Id</th><th>Prs</th><th>Preis</th><th>Grp</th>
	<th><table class=kontakt><tr><th>Id</th><th>Typ</th><th>Kontakt</th></tr></table></th>
	<th><table class=kontakt><tr><th>Id</th><th>Name</th><th>Vorname</th><th>Id</th><th>Typ</th><th>Kontakt</th></tr></table></th>
</tr>
<?php // ===============================================
foreach($kundenliste as $entry) { 
	$sEntry1   = implode("|",$entry);
	$sEntry2   = $entry['kundennummer'] . " " . $entry['nachname'] . " " . $entry['vorname'];

	$nr = $entry['kundennummer'];
	[$mk_kunde,$mk_adresse,$mk_kontakt,$mk_kp,$mk_tafel] = $db->getKundendaten($nr);
	Logger::debug_array("Main:: Kunde",$mk_kunde);
	Logger::debug_array("Main:: Adresse",$mk_adresse);
	Logger::debug_array("Main:: Kontakt",$mk_kontakt);
	Logger::debug_array("Main:: Kontaktperson",$mk_kp);
	Logger::debug_array("Main:: Tafel",$mk_tafel);
// =============================================== ?>

<tr>
	<td><?php echo $mk_kunde[0]['id']?></td>
	<td><?php echo $mk_kunde[0]['kundennummer']?></td>
	<td><?php echo $mk_kunde[0]['nachname']?></td>
	<td><?php echo $mk_kunde[0]['vorname']?></td>
	<td><?php echo $mk_kunde[0]['tafel']?></td>
	<td><?php echo $mk_kunde[0]['kaufhaus']?></td>
	<td><?php echo $mk_kunde[0]['gueltigkeit']?></td>
	<td><?php echo $mk_kunde[0]['nachweis']?></td>
	<td><?php echo $mk_kunde[0]['bemerkung']?></td>

	<td><?php echo $mk_adresse[0]['id']?></td>
	<td><?php echo $mk_adresse[0]['strasse']?></td>
	<td><?php echo $mk_adresse[0]['hausnummer']?></td>
	<td><?php echo $mk_adresse[0]['plz']?></td>
	<td><?php echo $mk_adresse[0]['ort']?></td>

<?php if(isset($mk_tafel) and isset($mk_tafel[0]['id'])) { ?>
	<td><?php echo $mk_tafel[0]['id']?></td>
	<td><?php echo $mk_tafel[0]['personen']?></td>
	<td><?php echo $mk_tafel[0]['preis']?></td>
	<td><?php echo $mk_tafel[0]['gruppe']?></td>
<?php } else { echo "<td colspan=4></td>"; } ?>

<td><table class=kontakt_nr>
<?php foreach($mk_kontakt as $kontakt) { ?>
	<tr>
	<td><?php echo $kontakt['id']?></td>
	<td><?php echo $kontakt['k_type']?></td>
	<td><?php echo $kontakt['kontakt']?></td>
	</tr>
<?php } ?>
</table></td>

<td style="padding:0px;"><table class="kontakt nomargin">
<?php foreach($mk_kp as $kp) { Logger::debug_array("Main:: Kontaktperson",$kp); ?>
	<tr>
	<td><?php echo $kp['id']?></td>
	<td><?php echo $kp['nachname']?></td>
	<td><?php echo $kp['vorname']?></td>
	<td><table class=kontakt_nr>
<?php foreach($kp['kontakte'] as $kpk) { Logger::debug_array("Main:: Kontakt",$kpk); ?>
	<tr>
	<td><?php echo $kpk['id']?></td>
	<td><?php echo $kpk['k_type']?></td>
	<td><?php echo $kpk['kontakt']?></td>
	</tr>
<?php } ?>
	</table></td>
	</tr>
<?php } ?>
</table></td>
<!--
 $mk_person  = ['id'=>0, 'kunde_id'=>0, 'p_type'=>'', 'vorname'=>'', 'nachname'=>'', 'error'=>''];
 $mk_tafel   = ['id'=>0, 'kunde_id'=>0, 'personen'=>0, 'preis'=>0, 'gruppe'=>'weiss', 'error'=>''];
 $mk_adresse = ['id'=>0, 'person_id'=>0, 'strasse'=>'', 'hausnummer'=>'', 'plz'=>'', 'ort'=>'', 'error'=>''];
 $mk_kontakt = [['id'=>0, 'person_id'=>0, 'k_type'=>'', 'kontakt'=>'', 'error'=>'']];
-->
</tr>
<?php } ?>

</table>
<?php 
} catch(Exception $e) {
	Logger::exception("liste.php",$e);
}
Logger::info("------------------------ Ende --------------------------"); ?>

<!-- ====================================================================================== -->
</body>
</html>
<!-- ################################################################## -->

