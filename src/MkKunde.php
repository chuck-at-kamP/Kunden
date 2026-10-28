<?php
require_once __DIR__ . '/Data.php';
/* SYNOPSIS
 * $kunde = new MkKunde($db);
 * $kunde->set($post);


 * $kunde->setPost($post);
 * $mk_kunde = $kunde->get($id);

 * Welche Methoden und Daten werden benötigt?
 *  - Daten:
 *    $default			- Default Werte.
 *    $nachweise        - Spezielle Aufzählungstypen, hier Gehaltsnachweis.
 *
 *  - Get Operationen:
 *    __toString()		- Lesbare Ausgabe Objekt
 *
 *  - SQL Operationen:
 *    sql_insert()		- von Data::write()
 *    sql_update()		- von Data::update()
 *    sql_delete()		- von Data::delete()
 *    sql_select()		- von Data::get()
 *
 * Was ist noch offen?
 *    setPost()				- ??? set($post) aus Klasse Data
 *    verify()				- Syntaxüberprüfung aller Daten.
 *    set_kundennummer()	- Syntaxüberprüfung einzelner Felder.
 */

class MkKunde extends Data {

	protected $default = ['id'=>0, 'barcode'=>'', 'kundennummer'=>'NONE', 'nachname'=>'None','vorname'=>'None',
		'tafel'=>false, 'kaufhaus'=>false, 'nachweis'=>'', 'gueltigkeit'=>'', 'bemerkung'=>'', 'changed'=>''];
	
	public $nachweise = ['ALG','Bafög','Bürgergeld','Rente','Grundsicherung','Gehalt','Wohngeld'];

	# ====================== Public  =========================

	/* Parent Konstruktor Data() wird genutzt.  */

	/* verify($mk_kunde) - Setzen der Kundendaten mit Überprüfung.
	 *
	 * Bevor die Daten in die Datenbank geschrieben werden, müssen diese überprüft werden.
	 */
	public function verify($mk_kunde) {
		$this->set_kundennummer($mk_kunde['kundennummer']);
		$this->set_nachweis($mk_kunde['nachweis']);
		$this->set_gueltigkeit($mk_kunde['gueltigkeit']);
		$this->set_type($mk_kunde['tafel'],$mk_kunde['kaufhaus']);
	}
	public function setPost($post) {
		if(isset($post['kaufhaus']) and $post['kaufhaus'] === "Kaufhaus") { $mk_kunde['kaufhaus'] = true; } 
		else { $mk_kunde['kaufhaus'] = false; }
		if(isset($post['tafel']) and $post['tafel'] === "Tafel") { $mk_kunde['tafel'] = true; } 
		else { $mk_kunde['tafel'] = false; }
		if(isset($post['kid'])) { $mk_kunde['id'] = intval($post['kid']); } else { $mk_kunde['id'] = 0; }
		if(isset($post['kundennummer'])) { $mk_kunde['kundennummer'] = $post['kundennummer']; } else { $mk_kunde['kundennummer'] = ""; }
		if(isset($post['nachweis'])) { $mk_kunde['nachweis'] = $post['nachweis']; } else { $mk_kunde['nachweis'] = ""; }
		if(isset($post['gueltigkeit'])) { $mk_kunde['gueltigkeit'] = $post['gueltigkeit']; } else { $mk_kunde['gueltigkeit'] = ""; }
		if(isset($post['bemerkung'])) { $mk_kunde['bemerkung'] = $post['bemerkung']; } else { $mk_kunde['bemerkung'] = ""; }
		$this->set($mk_kunde);
		return $this->data;
	}
	public function __toString() {
		if(isset($this->data['id']) and $this->data['id'] !== 0) { $id = $this->data['id']; }
		else { $id = 0; }
		if(isset($this->data['kundennummer']) and $this->data['kundennummer'] !== "NONE") { $knr = $this->data['kundennummer']; }
		else { $knr = "NONE"; }
		$str = "Kunde " . $knr . " (" . $id . ")";
		Logger::debug("MkKunde::__toString() Obj=" . $str);
		$post = $this->__toStringPost(); 
		if($id === 0 and $knr === "NONE" and $post !== "") { $str .= ", " . $post; }
		return $str;
	}
	public function __toStringPost() {
		if(isset($this->post['id'])) { $id = $this->post['id']; }
		else { $id = 0; }
		if(isset($this->post['kundennummer']) and $this->post['kundennummer'] !== "NONE") { $knr = $this->post['kundennummer']; }
		else { $knr = "NONE"; }
		if($knr === "NONE" and $id === 0) { $str = ""; }
		else { $str = "Post " . $knr . " (" . $id . ")"; }
		Logger::debug("MkKunde::__toStringPost() PostObj=" . $str);
		return $str;
	}
	# ====================== Protected =========================

	protected function set_kundennummer($value) {
		if(isset($value)) {
			if(preg_match("/[0-9]*\/[0-9]*/",$value)) { $this->data['kundennummer'] = $value; }
			else if($value === "") { $this->data['kundennummer'] = $value; }
			else { throw new InvalidArgumentException("Kundennummer hat falsches Format '$value' (nnnn/nnnn)!"); }
		}
		else { throw new InvalidArgumentException("Kundennummer ist Pflicht, kein Wert angegeben!"); }
	}

	protected function set_nachweis($value) {
		if(isset($value)) {
			if(in_array($value, $this->nachweise)) { $this->data['nachweis'] = $value; }
			else if($value === "") { $this->data['nachweis'] = $value; }
			else { throw new InvalidArgumentException("Nachweis nicht erlaubt '$value'!"); }
		}
		else { throw new InvalidArgumentException("Nachweis ist Pflicht, kein Wert angegeben!"); }
	}

	protected function set_gueltigkeit($value) {
		if(isset($value)) {
			if($value === "") { $value = "1970-01-01"; }
		}
		else { $value = "1970-01-01"; }
		[$ret,$value] = $this->convertDate($value);
		if(!$ret) { throw new InvalidArgumentException("Falsches Datum '" . $value . "'"); }
		else { $this->data['gueltigkeit'] = $value; }
	}

	protected function set_type($tafel,$kaufhaus) {
		if(!$tafel and !$kaufhaus) {
			throw new InvalidArgumentException("Kundentyp 'Tafel' oder 'Kaufhaus' muss gesetzt sein!");
		}
		else {
			$this->data['tafel'] = $tafel;
			$this->data['kaufhaus'] = $kaufhaus;
			return true;
		}
	}
	# ====================== Database =========================
	protected function sql_insert() {
		Logger::debug_array("MkKunde::sql_insert() Kunde ",$this->post);
		if($this->post['tafel']) $t = "true"; else $t = "false";
		if($this->post['kaufhaus']) $k = "true"; else $k = "false";
		$values = "VALUES ('" . $this->post['kundennummer'] . "', " . $t . "," . $k . ",'" . 
			$this->post['vorname'] . "','" . $this->post['nachname'] . "','" .
			$this->post['nachweis'] . "','" . $this->post['gueltigkeit'] . "','" . $this->post['bemerkung'] . "')";
		$sql = "INSERT INTO `mk_kunde` (`kundennummer`,`tafel`,`kaufhaus`,`vorname`,`nachname`,`nachweis`,`gueltigkeit`,`bemerkung`) $values";
		$sql .= " RETURNING id, kundennummer";
		Logger::debug("MkKunde::sql_insert() SQL - $sql");
		return $sql;
	}
	protected function sql_update() {
		Logger::debug_array("MkKunde::sql_update() Kunde ",$this->post);

		# if(isset($this->post['tafel'])) $t = "true"; else $t = "false";
		# if(isset($this->post['kaufhaus'])) $k = "true"; else $k = "false";
		# $values = "kundennummer='" . $this->post['kundennummer'] . "', tafel=" . $t . ", kaufhaus=" . $k . ", nachweis='" . 
		# 	$this->post['nachweis'] . "', gueltigkeit='" . $this->post['gueltigkeit'] . "', bemerkung='" . $this->post['bemerkung'] . "'";

		$array = [];
		foreach(array_keys($this->post) as $key) {
			if($key !== "id") array_push($array, $key . "='" . $this->post[$key] . "'");
		}
		$values = implode(", ",$array);

		$sql = "UPDATE `mk_kunde` SET $values WHERE id=" . $this->post['id'];
		# $sql .= " RETURNING id, kundennummer";
		Logger::debug("MkKunde::sql_update() SQL - $sql");
		return $sql;
	}
	protected function sql_delete() {
		$sql = "DELETE FROM mk_kunde WHERE id=" . $this->post['id'] . " RETURNING id, kundennummer";
		Logger::debug("MkKunde::sql_delete() - $sql");
		return $sql;
	}
	protected function sql_select($nr,$type="None") {
		Logger::debug("MkKunde::sql_select() Kundennummer '$nr'");
		$sql = "select k.* from mk_kunde k where k.kundennummer='$nr'";
		# $tmp = $this->db->query($sql);
		# $this->post = $tmp[0];
		return $sql;
	}
}


