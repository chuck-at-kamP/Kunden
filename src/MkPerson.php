<?php
require_once __DIR__ . '/Data.php';
/* SYNOPSIS
 * $kunde = new MkKunde($db);
 * $kunde->set($mk_kunde);
 * $mk_kunde = $kunde->get($id);
 */

class MkPerson extends Data {

	protected $default  = ['id'=>0, 'kunde_id'=>0, 'vorname'=>'None', 'nachname'=>'None'];
	
	# ====================== Public  =========================

	public function verify($mk_person) {
		$this->data = $mk_person;
	}

	public function setPost($post) {
		if(isset($post['pid'])) { $mk_person['id'] = intval($post['pid']); } else { $mk_person['id'] = 0; }
		if(isset($post['p_type'])) { $mk_person['p_type'] = $post['p_type']; } else { $mk_person['p_type'] = "Kunde"; }
		if(isset($post['kunde_id'])) { $mk_person['kunde_id'] = intval($post['kunde_id']); } else { $mk_person['kunde_id'] = 0; }
		if(isset($post['nachname'])) { $mk_person['nachname'] = $post['nachname']; } else { $mk_person['nachname'] = ""; }
		if(isset($post['vorname'])) { $mk_person['vorname'] = $post['vorname']; } else { $mk_person['vorname'] = ""; }
		$this->set($mk_person);
		return $this->data;
	}
	public function __toString() {
		if(isset($this->data['vorname']) and $this->data['vorname'] !== "None") { $vor = $this->data['vorname']; }
		else { $vor = "None"; }
		if(isset($this->data['nachname']) and $this->data['nachname'] !== "None") { $nach = $this->data['nachname']; }
		else { $nach = "None"; }
		return "Person " . $vor . " " . $nach . " (" .  $this->data['id'] . "/" . $this->data['kunde_id'] . ")";
	}

	public function set_id($id) {
		$this->data['kunde_id'] = $id;
	}

	# ====================== Database =========================

	protected function sql_insert() {
		$values = "VALUES (".$this->post['kunde_id'].", '".$this->post['vorname']."','" . $this->post['nachname']."')";
		$sql = "INSERT INTO `mk_person` (`kunde_id`,`vorname`,`nachname`) $values";
		$sql .= " RETURNING id, kunde_id, vorname, nachname";
		Logger::debug("MkPerson::sql_insert() SQL - $sql");
		return $sql;
	}
	protected function sql_update() {
		Logger::debug_array("MkPerson::sql_update() Kunde ",$this->post);
		$array = [];
		foreach(array_keys($this->post) as $key) {
			if($key !== "id") array_push($array, $key . "='" . $this->post[$key] . "'");
		}
		$values = implode(", ",$array);
		$sql = "UPDATE `mk_person` SET $values WHERE id=" . $this->post['id'];
		Logger::debug("MkPerson::sql_update() SQL - $sql");
		return $sql;
	}

	protected function sql_delete() {
		$sql = "DELETE FROM mk_person WHERE id=" . $this->post['id'] . " RETURNING id, kunde_id, vorname, nachname";
		Logger::debug("MkPerson::sql_delete() - $sql");
		return $sql;
	}

	protected function sql_select($id,$type="None") {
		if($type === "Kunde") $filter = "p.kunde_id='$id'";
		else $filter = "p.id='$id'";
		Logger::debug("MkPerson::sql_select() Type '$type' Id '$id'");
		$sql = "select p.* from mk_person p where $filter";
		return $sql;
	}
}

