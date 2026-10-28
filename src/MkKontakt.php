<?php
require_once __DIR__ . '/Data.php';
/* SYNOPSIS
 * $kunde = new MkKunde($db);
 * $kunde->set($mk_kunde);
 * $mk_kunde = $kunde->get($id);
 */

class MkKontakt extends Data {

	protected $default  = ['id'=>0, 'kunde_id'=>0, 'k_type'=>'None', 'kontakt'=>'None'];

	public $kontakt_types = ['Email','Festnetz','Mobil'];

	# ====================== Public  =========================

	public function verify($mk_adresse) {
	}

	public function setPost($post) {
	}

	public function setPostSingle($post,$i) {
		if(isset($post["kontakt_type_$i"])) { $mk_kontakt['k_type'] = $post["kontakt_type_$i"]; } else { $mk_kontakt['id'] = 0; }
		if(isset($post["kontakt_$i"])) { $mk_kontakt['kontakt'] = $post["kontakt_$i"]; } else { $mk_kontakt['kontakt'] = ""; }
		if(isset($post["ktid_$i"])) { $mk_kontakt['id'] = $post["ktid_$i"]; } else { $mk_kontakt['id'] = 0; }
		if(isset($post["person_id_kontakt_$i"])) { 
			$mk_kontakt[$i]['person_id'] = $post["person_id_kontakt_$i"]; } else { $mk_kontakt['person_id'] = 0; }
	}
	public function __toString() {
		if(isset($this->data['k_type']) and $this->data['k_type'] !== "None") { $kty = $this->data['k_type']; }
		else { $kty = "None"; }
		if(isset($this->data['kontakt']) and $this->data['kontakt'] !== "None") { $kon = $this->data['kontakt']; }
		else { $kon = "None"; }
		$ret = "Kontakt " . $kty . " " . $kon . " (" . $this->data['id'] . "/" . $this->data['kunde_id'] . ")";
		return $ret;
	}
	public function set_id($id) {
		$this->data['kunde_id'] = $id;
	}
	# ====================== Protected =========================

	# ====================== Database =========================

	protected function sql_insert() {
		$values = "VALUES (".$this->post['kunde_id'].", '".$this->post['k_type']."', '".$this->post['kontakt']."')";
		$sql = "INSERT INTO `mk_kontakt` (`kunde_id`,`k_type`,`kontakt`) $values";
		$sql .= " RETURNING id, kunde_id, kontakt";
		Logger::debug("MkKontakt::sql_insert() SQL - $sql");
		return $sql;
	}
	protected function sql_delete() {
		$sql = "DELETE FROM mk_kontakt WHERE id=" . $this->data['id'] . " RETURNING id, kunde_id, k_type, kontakt";
		Logger::debug("MkKontakt::sql_delete() - $sql");
		return $sql;
	}
	protected function sql_update() {
		Logger::debug_array("MkKontakt::sql_update() Kontakt ",$this->post);
		$array = [];
		foreach(array_keys($this->post) as $key) {
			if($key !== "id") array_push($array, $key . "='" . $this->post[$key] . "'");
		}
		$values = implode(", ",$array);
		$sql = "UPDATE `mk_kontakt` SET $values WHERE id=" . $this->post['id'];
		Logger::debug("MkKontakt::sql_update() SQL - $sql");
		return $sql;
	}
	protected function sql_select($id,$type="None") {
		if($type === "Kunde") $filter = "k.kunde_id='$id'";
		else if($type === "Person") $filter = "k.person_id='$id'";
		else $filter = "k.id='$id'";
		Logger::debug("MkKontakt::sql_select() $type Id '$id'");
		$sql = "select k.* from mk_kontakt k where $filter";
		return $sql;
	}
}


