<?php
require_once __DIR__ . '/src/bootstrap.php';
require_once __DIR__ . '/src/Database.php';
Logger::info("\n\n#####################################################################################");
Logger::info("----------------- Start kunden.php ----------------------");

session_start();

$app = "/kunden/kunden.php";
$_REQUEST['application'] = $app;
$_SESSION['application'] = $app;
Logger::session($_SESSION);
if (!isset($_SESSION["admin_logged_in"]) || $_SESSION["admin_logged_in"] !== true) {
	Logger::info("Redirect to Login ...");
    header("Location: /mkadmin/login.php?application=" . $app);
    exit;
} else if(!isset($_SESSION["roles_user"]) || !str_contains($_SESSION["roles_user"],"kn")) {
	Logger::info("Redirect to Error ...");
    header("Location: /error.html");
    exit;
} else {
	Logger::info("Login successful ...");
	Logger::audit("Aufruf kunden.php.");
}

/* Die Daten sind entsprechend der Felder der Datenbank aufgebaut.  */
$empty_data = [
	['id'=>0, 'barcode'=>'', 'kundennummer'=>'', 'tafel'=>false, 'kaufhaus'=>false, 
		'nachweis'=>'', 'gueltigkeit'=>'', 'bemerkung'=>'', 'changed'=>'', 'error'=>'' ],
	['id'=>0, 'kunde_id'=>0, 'p_type'=>'', 'vorname'=>'', 'nachname'=>'', 'error'=>''],
	['id'=>0, 'person_id'=>0, 'strasse'=>'', 'hausnummer'=>'', 'plz'=>'', 'ort'=>'', 'error'=>''],
	['id'=>0, 'kunde_id'=>0, 'personen'=>0, 'preis'=>0, 'gruppe'=>'weiss', 'error'=>''],
	[['id'=>0, 'person_id'=>0, 'k_type'=>'', 'kontakt'=>'', 'error'=>'']],
];

$nachweise = ['ALG','Bafög','Bürgergeld','Rente','Grundsicherung','Gehalt','Wohngeld'];
$kontakt_types = ['Email','Festnetz','Mobil'];
# $action = "";
$action_type = "Select_Customer";
$action_sub_type = "Neuer_Kunde";
$action_gui = "None";

$fehler = "OK"; # Initialisierung Fehler
$exception = false;
$post_error = "";

/*
action_type shows what button is pressed:
	"Select_Customer"   - Select field for Customer "Kunden" is changed.
	"Change_Customer"   - "Speichern" oder "Löschen" Button is pressed.
	"Add_Contact"       - "+" Button for "Kontakt" is pressed.
	"Add_Contactperson" - "+" Button for "Kontaktperson" is pressed.
	"Select_Tafel"      - "x" Selection for Tafel.
	"Select_Kaufhaus"   - "x" Selection for Kaufhaus.
	"Button_Change"     - "Speichern" Button is pressed.
	"Button_Delete"     - "Löschen" Button is pressed.
	"kunden" == "Neuer_Kunde" - Neuer Kunde ist ausgewält.
*/

Logger::info("MAIN:: Set Error Handler");	# ErrorHandler habe ich gebaut um Warnings abzufangen, diese werden dann in $post_error dargestellt.
set_error_handler(function($severity, $message, $file, $line) {
    if (!(error_reporting() & $severity)) {
		Logger::info("MAIN:: Error Handler");
        return;
    }
    throw new ErrorException($message, 0, $severity, $file, $line);
});
# -------------- Kundenliste ---------------
Logger::info("MAIN:: App initialize ... (Status 1: " . $fehler . ")");
$APP = new App();
try {
	Logger::info("MAIN:: App Get Kundenliste ...");
	$kundenliste = $APP->getKundenliste();
	$db = $APP->getDb();
} catch(Exception $e) {
	$exception = true;
	$fehler = "Fehler: " . $e->getMessage();
	Logger::exception("MAIN:: Read customer failed. kunde=" . $mk_kunde['kundennummer'], $e);
}
Logger::info("MAIN:: (Status 2: " . $fehler . ")");

# -------------- POST ----------------------
if(!empty($_POST)) {
	Logger::debug_array("Main:: Received Post ...", $_POST, true);
	try {
		[$mk_kunde,$mk_adresse,$mk_tafel,$mk_kontakt] = $APP->distribute($_POST);
	} catch(Exception $e) {
		$exception = true;
		$fehler = "Exception: " . $e->getMessage();
		Logger::exception("MAIN:: Distribute Error",$e);
	} catch(TypeError $e) {
		$exception = true;
		$fehler = "TypeError: " . $e->getMessage();
		Logger::exception("MAIN:: Distribute TypeError",$e);
	} 
	[$action_type,$action_sub_type] = $APP->getAction();
	Logger::info("MAIN:: Get Action: " . $action_type . " " . $action_sub_type);
	$fehler_array = $APP->getMsg();
	# Logger::info("MAIN:: (Status 3: \n" . implode("\n",$fehler_array) . ")");
}
else {
	Logger::info("MAIN:: Get Kundendaten ...");
	if($db) {
		if($kundenliste) {
			$anz = count($kundenliste);
			$fehler = "OK - $anz Kunden aus Datenbank gelesen.";
		} else {
			$fehler = "OK - Datenbank geöffnet.";
		}
	} 
	[$mk_kunde,$mk_adresse,$mk_tafel,$mk_kontakt] = $APP->getKundendaten();
	Logger::debug(__LINE__ . " MAIN:: Test Kunde: " . json_encode($mk_kunde));
	Logger::debug(__LINE__ . " MAIN:: Test Adresse: " . json_encode($mk_adresse));
	Logger::debug(__LINE__ . " MAIN:: Test Tafel: " . json_encode($mk_tafel));
	Logger::debug(__LINE__ . " MAIN:: Test Kontakt: " . json_encode($mk_kontakt));
	Logger::info("MAIN:: (Status 4: " . $fehler . ")");
}

# --------------------- Darstellung Fehler ---------------------
Logger::info(__LINE__ . " MAIN:: Darstellung Status ...");
if($exception) {
	Logger::error(__LINE__ . " MAIN:: Catched exception=" . $fehler);
	[$mk_kunde,$mk_adresse,$mk_tafel,$mk_kontakt] = $APP->get(); # Damit nach einem Fehler sich die GUI wieder aufbaut.
}
$fehler = Fehler::getMessage($action_type, $fehler);
if(isset($fehler_array)) {
	Logger::debug_array(__LINE__ . " MAIN:: Fehler Array",$fehler_array);
	$fehler_array_text = Fehler::getMessageFromArray($fehler_array);
	$fehler = Fehler::frame($fehler . "<br>" . $fehler_array_text);
} else {
	$fehler = Fehler::frame($fehler);
}
Logger::info(__LINE__ . " MAIN:: (Status 5: " . $fehler . ")");

Logger::info(__LINE__ . " MAIN:: ======================== Build GUI =======================");
Logger::info(__LINE__ . " MAIN:: Action: " . $action_type . " " . $action_sub_type . " " . $action_gui);
try {
?>
<!DOCTYPE html> 
<html> 
<head><link rel="stylesheet" href="html/style.css">
	<link rel="icon" type="image/png" href="/kunden/html/favicon.png">
	<script type="text/javascript" src="/kunden/html/kunden.js"></script>
</head> 
<body> 

<table class="frame">
<!-- ############################################################################# -->
<tr><td class="frame title" colspan=2>
<?php include "include/header.php"; ?>
</td></tr>
<!-- ============================ Fehlerausgabe  ====================================== -->
<tr><td>
<?php echo $fehler ?>
</td></tr>
<!-- ============================ Eingabe Daten  ====================================== -->
<tr><td class="frame form">
<form id="form_kunden" action=<?php echo '"' . ACTION_KUNDE . '"'; ?> method="post">
	<input type="text" id="action_type"     name="action_type"     value="<?php echo $action_type; ?>" hidden/>
	<input type="text" id="action_sub_type" name="action_sub_type" value="<?php echo $action_sub_type; ?>" hidden/>
	<input type="text" id="action_gui"      name="action_gui"      value="<?php echo $action_gui; ?>" hidden/>


<!-- ================= Kundenliste ============================= -->
<table class="kunden form">
	<tr>
	<td><label for="kunden">Kunden:</label></td>
	<td>
		<select id="kunden" name="kunden" onChange="javascript:send('Select_Customer','None'); ">
		<option value="" disabled hidden> -- Kunden auswählen -- </option>
		<option value="Neuer_Kunde" 
		<?php if($action_type==="Select_Customer" and $action_sub_type==="Neuer_Kunde") { echo "selected"; } ?>> 
			Neuer Kunde </option>
			<?php // ===============================================
			foreach($kundenliste as $entry) { 
				$kSelected = (isset($mk_kunde[0]['kundennummer']) and ($mk_kunde[0]['kundennummer'] === $entry['kundennummer']));
				$sEntry1   = implode("|",$entry);
				$sEntry2   = $entry['kundennummer'] . " " . $entry['nachname'] . " " . $entry['vorname']
				// =============================================== ?>
				<option value="<?php echo $sEntry1 ?>" 
					<?php if($kSelected) { echo "selected"; } ?>> <?php echo $sEntry2 ?> </option>
			<?php } ?>
		</select>
	</td>
	</tr>
	<tr><td colspan=2><hr></td></tr>

<!-- ================= Kunde (mk_kunde) =================== -->
	<?php include "include/kunde.php"; ?>
	<tr><td colspan=2><hr></td></tr>

<!-- ================= Person (mk_person) ================= -->
<!--
	<?php // $mk_kunde['p_type'] = "Kunde"; include "include/person.php"; ?>
	<tr><td colspan=2><hr></td></tr>
-->

<!-- ====================== Adresse (mk_adresse) ======================== -->
	<?php include "include/adresse.php"; ?>
	<tr><td colspan=2><hr></td></tr>

<!-- ====================== Kontakt (mk_kontakt) ======================== -->
	<tr><td colspan=2><table class="Kontakt">
	<?php include "include/kontakt.php"; ?>
<?php if($action_type === "Add_Contact") { $kontakt_element['k_type'] = ""; $kontakt_element['kontakt'] = ""; include "include/kontakt.php"; } ?>
	</table></td></tr>
<?php $text="Kontakt"; $action_type="'Add_Contact'"; include "include/line.php"; ?>

<!-- ====================== Kontaktperson ======================== -->
	<tr><td colspan=2><hr></td></tr>
	<tr><td colspan=2><table class="Kontaktperson">
	<?php if(isset($mk_kp)) { 
		foreach($mk_kp as $kontaktperson_element) {
			if(isset($kontaktperson_element) AND isset($kontaktperson_element['id'])) {
				Logger::debug("Include Kontaktperson: " . $kontaktperson_element['id']);
				if(is_int($kontaktperson_element['id'])) { 
					include "include/kontaktperson.php";
					$mk_kontakt = $kontaktperson_element['kontakte'];
					include "include/kontakt.php";
				}
			}
		}
	}
	?>
<?php if($action_type === "Add_Contactperson") { 
	$kontaktperson_element['id'] = ""; $kontaktperson_element['nachname'] = ""; $kontaktperson_element['vorname'] = ""; include "include/kontaktperson.php";
	$kontakt_element['k_type'] = ""; $kontakt_element['kontakt'] = ""; include "include/kontakt.php";
} ?>
	</table></td></tr>
<?php $text="Kontaktperson"; $action_type="'Add_Contactperson'"; 
	Logger::debug("Set action_type 1: " . $action_type);
	include "include/line.php"; ?>

<!-- ====================== Tafel (mk_tafel) ======================== -->
	<?php include "include/tafel.php"; ?>
	<tr><td colspan=2><hr></td></tr>

<?php } catch(Exception $e) {
	$post_error = "FEHLER " . $e->getMessage();
	Logger::exception("MAIN:: Posterror", $e);
}
?>
<!-- ====================== Submit ================================== -->
	<tr><td class="submit" colspan=2>
	<?php if($action_sub_type === "Neuer_Kunde") { ?>
		<input type="submit" name="Button_Save" value="Speichern">
	<?php } else { ?>
		<input type="submit" name="Button_Change" value="Ändern">
		<input type="submit" name="Button_Delete" value="Löschen">
	<?php } ?>
	</td></tr>
</table></form>
</td></tr>
<tr><td><pre><?php echo $post_error ?></pre></td></tr>
</table>
<?php Logger::info("------------------------ Ende --------------------------"); ?>

<!-- Das Script muss am Ende aufgerufen werden, damit die Felder "action_gui" und "kontakt_hinzu" bekannt sind. -->
<script type="text/javascript" src="/kunden/html/post_kunden.js"></script>
<!-- ====================================================================================== -->
</body>
</html>
<!-- ################################################################## -->
