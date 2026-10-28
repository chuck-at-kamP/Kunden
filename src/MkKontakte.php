<?php
require_once __DIR__ . '/Data.php';
require_once __DIR__ . '/MkKontakt.php';
/* SYNOPSIS
 * $kontakte = new MkKontakte($db);
 * $kontakte->add($mk_kontakt);


 * $kunde->set($mk_kunde);
 * $mk_kunde = $kunde->get($id);
 */

class MkKontakte {

	protected $objs  = [];
	protected $db;

	# ====================== Public  =========================
	public function __construct($db) {
		$this->db = $db;			# Set DB.
	}

	// get() liest mehrere Kontakte aus DB.
	public function get($id = 0, $type = "None") { // $type = "Kunde" oder "Person"
		$obj = new MkKontakt($this->db);
		$array = $obj->getAll($id,$type);
		foreach($array as $k) {
			$tmp = new MkKontakt($this->db);
			$tmp->setData($k);
			array_push($this->objs,$tmp);
			Logger::debug("MkKontakte::get() Kontakt: " . json_encode($k));
		}
		return $this->getData();
	}

	public function getData() {
		$array = [];
		foreach($this->objs as $o) {
			array_push($array, $o->getData());
		}
		Logger::debug("MkKontakte::getData() Kontakte: " . json_encode($array));
		return $array;
	}
	public function add($mk_kontakt) {}	// TBD

	public function verify($mk_data) {} // TBD

	public function __toString() {
		$nr = count($this->objs);
		$ret = "Kontakte ($nr): ";
		$array = [];
		foreach($this->objs as $k) { array_push($array,$k->__toString()); }
		$str = $ret . join(",",$array);
		Logger::debug("MkKontakte::__toString() " . $str);
		return $str;
	}
	public function getMsg() {
		$msg = [];
		foreach($this->objs as $o) { array_push($msg, $o->getMsg()); }
		return $msg;
	}
	public function setPost($post) {
		foreach($this->objs as $o) { $o->setPost($post); }
	}

	# ====================================================
	/*
	public function set($mk_kontakte) {
		$this->data = $mk_kontakte;
	}
	public function setPost($post) {
		$mk_kontakte = [];
		for($i=0; $i<10;$i++) {
			Logger::debug("MkKontakte::setPost() Loop i=" . $i);
			if(isset($post["kontakt_type_$i"]) or isset($post["kontakt_$i"])) { 
				$k = new MkKontakt($this->db);
				$k->setPostSingle($post,$i);
				array_push($mk_kontakte,$k);
				array_push($this->data,$k);
			}
		}
		return $this->data;
	}
	public function set_id($id) {
		foreach($this->data as $k) { 
			Logger::info("MkKontakte::set_id() " . $k);
			$k->set_id($id); 
		}
	}

	public function write() {
		foreach($this->data as $k) { 
			Logger::info("MkKontakte::write() " . $k);
			$k->write(); 
		}
	}
	public function delete() {
		foreach($this->data as $k) { 
			Logger::info("MkKontakte::delete() " . $k);
			$k->delete(); 
		}
	}


	

	public function getMsg() {
		$msg = [];
		foreach($this->data as $k) { array_push($msg, $k->getMsg()); }
		return $msg;
	}
	*/
	# ====================== Database =========================

	/*
	protected function sql_select($person_id,$type="None") {
		Logger::debug("MkKontakte::sql_select() Id Person '$person_id'");
		$sql = "select k.* from mk_kontakt k where k.person_id='$person_id'";
		$tmp = $this->db->query($sql);
		foreach($tmp as $k) {
			$o = new MkKontakt($this->db);
			$o->set($k);
			Logger::debug("MkKontakte::sql_select() Kontakt '" . $o . "'");
			array_push($this->data,$o);
		}
		return $this->get();
	}
	*/
}


