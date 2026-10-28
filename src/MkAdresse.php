<?php
require_once __DIR__ . '/Data.php';
/* SYNOPSIS
 * $kunde = new MkKunde($db);
 * $kunde->set($mk_kunde);
 * $mk_kunde = $kunde->get($id);
 */

class MkAdresse extends Data {

	protected $default  = ['id'=>0, 'kunde_id'=>0, 'strasse'=>'None', 'hausnummer'=>'None', 'plz'=>'None', 'ort'=>''];

	# ====================== Public  =========================

	public function verify($mk_adresse) {
	}

	public function setPost($post) {
		if(isset($post['aid'])) { $mk_adresse['id'] = $post['aid']; } else { $mk_adresse['id'] = 0; }
		if(isset($post['person_id'])) { $mk_adresse['person_id'] = $post['person_id']; } else { $mk_adresse['person_id'] = 0; }
		if(isset($post['strasse'])) { $mk_adresse['strasse'] = $post['strasse']; } else { $mk_adresse['strasse'] = ""; }
		if(isset($post['hausnummer'])) { $mk_adresse['hausnummer'] = $post['hausnummer']; } else { $mk_adresse['hausnummer'] = ""; }
		if(isset($post['plz'])) { $mk_adresse['plz'] = $post['plz']; } else { $mk_adresse['plz'] = ""; }
		if(isset($post['ort'])) { $mk_adresse['ort'] = $post['ort']; } else { $mk_adresse['ort'] = ""; }
		$this->set($mk_adresse);
		return $this->data;
	}
	public function __toString() {
		if(isset($this->data['plz']) and $this->data['plz'] !== "None") { $plz = $this->data['plz']; }
		else { $plz = "None"; }
		if(isset($this->data['strasse']) and $this->data['strasse'] !== "None") { $str = $this->data['strasse']; }
		else { $str = "None"; }
		if(isset($this->data['hausnummer']) and $this->data['hausnummer'] !== "None") { $hnr = $this->data['hausnummer']; }
		else { $hnr = "None"; }
		return "Adresse " . $plz . ", " . $str . " " . $hnr . " (" . $this->data['id'] . "/" . $this->data['kunde_id'] . ")";
	}
	public function set_id($id) {
		$this->data['kunde_id'] = $id;
	}
	# ====================== Protected =========================

	# ====================== Database =========================

	protected function sql_insert() {
		$values = "VALUES (".$this->post['kunde_id'].", '".$this->post['strasse']."', '".$this->post['hausnummer']."','". 
			$this->post['plz']."', '".$this->post['ort']."')";
		$sql = "INSERT INTO `mk_adresse` (`kunde_id`,`strasse`,`hausnummer`,`plz`,`ort`) $values";
		$sql .= " RETURNING id, kunde_id, strasse, hausnummer";
		Logger::debug("MkAdresse::sql_insert() SQL - $sql");
		return $sql;
	}
	protected function sql_update() {
		Logger::debug_array("MkAdresse::sql_update() Adresse ",$this->post);
		$array = [];
		foreach(array_keys($this->post) as $key) {
			if($key !== "id") array_push($array, $key . "='" . $this->post[$key] . "'");
		}
		$values = implode(", ",$array);
		$sql = "UPDATE `mk_adresse` SET $values WHERE id=" . $this->post['id'];
		Logger::debug("MkAdresse::sql_update() SQL - $sql");
		return $sql;
	}
	protected function sql_delete() {
		$sql = "DELETE FROM mk_adresse WHERE id=" . $this->post['id'] . " RETURNING id, kunde_id, strasse, hausnummer";
		Logger::debug("MkAdresse::sql_delete() - $sql");
		return $sql;
	}
	protected function sql_select($id,$type="None") {
		if($type === "Kunde") $filter = "a.kunde_id='$id'";
		else if($type === "Person") $filter = "a.person_id='$id'";
		else $filter = "a.id='$id'";
		Logger::debug("MkAdresse::sql_select() $type Id '$id'");
		$sql = "select a.* from mk_adresse a where $filter";
		return $sql;
	}
}


