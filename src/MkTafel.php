<?php
require_once __DIR__ . '/Data.php';
/* SYNOPSIS
 * $kunde = new MkKunde($db);
 * $kunde->set($mk_kunde);
 * $mk_kunde = $kunde->get($id);
 */

class MkTafel extends Data {

	protected $default  = ['id'=>0, 'kunde_id'=>0, 'personen'=>0, 'preis'=>0, 'gruppe'=>'None'];

	public $groups = ['Email','Festnetz','Mobil'];
	
	# ====================== Public  =========================

	public function verify($mk_adresse) {
	}

	public function setPost($post) {
		if(isset($post['tid'])) { $mk_tafel['id'] = $post['tid']; } else { $mk_tafel['id'] = 0; }
		if(isset($post['kunde_id_tafel'])) { $mk_tafel['kunde_id'] = $post['kunde_id_tafel']; } else { $mk_tafel['kunde_id'] = 0; }
		if(isset($post['personen'])) { $mk_tafel['personen'] = $post['personen']; } else { $mk_tafel['personen'] = 0; }
		if(isset($post['preis'])) { $mk_tafel['preis'] = $post['preis']; } else { $mk_tafel['preis'] = 0; }
		if(isset($post['gruppe'])) { $mk_tafel['gruppe'] = $post['gruppe']; } else { $mk_tafel['gruppe'] = ""; }
		$this->set($mk_tafel);
		return $this->data;
	}
	public function __toString() {
		if(isset($this->data['personen']) and $this->data['personen'] !== "None") { $pers = $this->data['personen']; }
		else { $pers = 0; }
		if(isset($this->data['preis']) and $this->data['preis'] !== "None") { $preis = $this->data['preis']; }
		else { $preis = 0; }
		if(isset($this->data['gruppe']) and $this->data['gruppe'] !== "None") { $grp = $this->data['gruppe']; }
		else { $grp = "None"; }
		return "Tafel " . $grp . " " . $pers . "/" . $preis . " (" .
			$this->data['id'] . "/" . $this->data['kunde_id'] . ")";
	}
	public function set_id($id) {
		$this->data['kunde_id'] = $id;
	}
	# ====================== Protected =========================

	# ====================== Database =========================

	protected function sql_insert() {
		$values = "VALUES ('".$this->post['kunde_id']."', ".$this->post['personen'].", ".$this->post['preis'].", '".
			$this->post['gruppe']."')";
		$sql = "INSERT INTO `mk_tafel` (`kunde_id`,`personen`,`preis`,`gruppe`) $values";
		$sql .= " RETURNING id, kunde_id, gruppe";
		Logger::debug("MkTafel::sql_insert() SQL - $sql");
		return $sql;
	}
	protected function sql_delete() {
		$sql = "DELETE FROM mk_tafel WHERE id=" . $this->post['id'] . " RETURNING id, kunde_id, gruppe";
		Logger::debug("MkTafel::sql_delete() - $sql");
		return $sql;
	}
	protected function sql_update() {
		Logger::debug_array("MkTafel::sql_update() Tafel ",$this->post);
		$array = [];
		foreach(array_keys($this->post) as $key) {
			if($key !== "id") array_push($array, $key . "='" . $this->post[$key] . "'");
		}
		$values = implode(", ",$array);
		$sql = "UPDATE `mk_tafel` SET $values WHERE id=" . $this->post['id'];
		Logger::debug("MkTafel::sql_update() SQL - $sql");
		return $sql;
	}
	protected function sql_select($id,$type="None") {
		Logger::debug("MkTafel::sql_select() Id Kunde '$id'");
		$sql = "select t.* from mk_tafel t where t.id='$id'";
		return $sql;
	}
}


