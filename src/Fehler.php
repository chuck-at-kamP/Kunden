<?php
class Fehler {

	/*
		SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry '004/26' for key 'kundennummer'
	*/
	protected static $fehler = [
		["~SQLSTATE\[23000\]: Integrity constraint violation: 1062 Duplicate entry '(.*)' for key '(.*)'~",
		 "Eintrag doppelt vorhanden!"],
		["~SQLSTATE\[42S22\]: Column not found: 1054 Unknown column '(.*)' in '(.*)'~", "Unbekannte Spalte!"],
		["~SQLSTATE\[23000\]: Integrity constraint violation: 1452 Cannot add or update a child row: a foreign key constraint fails \(`dbkunden`.`(.*)`, CONSTRAINT .* FOREIGN KEY \(`(.*)`\) .*~", "Abhängigkeiten vorhanden!"],
		["~SQLSTATE\[23000\]: Integrity constraint violation: 1451 Cannot delete or update a parent row: a foreign key constraint fails \(`dbkunden`.`(.*)`, CONSTRAINT .* FOREIGN KEY \(`(.*)`\) .*~", "Abhängigkeiten vorhanden!"],
	];

	public static function getMessage($action, $error_message) {
		if(isset($error_message) and $error_message !== "") {
			// Logger::debug(__LINE__ . " Fehler::getMessage() In: action=" . $action . ", mess=" . $error_message);
			$text = $error_message;
			$i = 0;
			foreach(Fehler::$fehler as $entry) {
				// Logger::debug(__LINE__ . " Fehler::getMessage() Regex i=" . $i . ", entry=" . $entry[0]);
				if(preg_match($entry[0],$error_message,$a)) {
					// Logger::debug_array(__LINE__ . " Fehler::getMessage() Found2 ", $a);
					$text = $entry[1] . " " . $a[2] . ", " . $a[1];
				}
				$i++;
			}
			if(isset($action) and $action !== "") { 
				$text = $action . ": " . $text; 
			}
			else { $text = "Fehler: " . $text; }
			// Logger::debug(__LINE__ . " Fehler::getMessage() Out: " . $text);
			return $text;
		} else { return null; }
	}
	public static function getMessageFromArray($array) {
		$new_array = [];
		// Logger::debug_array(__LINE__ . " Fehler::getMessageFromArray()", $array);
		foreach($array as $e) {
			$txt = Fehler::getMessage("", $e);
			if(isset($txt)) { array_push($new_array,$txt); }
		}
		$txt = implode("<br>",$new_array);
		return $txt;
	}
	public static function frame($fehler) {
		if(!isset($fehler) or str_contains($fehler,"OK") or !str_contains($fehler,"Fehler")) {
			$ret = '<div class="error" style="border:4px solid green">' . $fehler . '</div>';
		}   
		else {
			$ret = '<div class="error">' . $fehler . '</div>';
		}
		return $ret;
	}
}





