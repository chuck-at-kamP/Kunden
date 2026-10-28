<?php
abstract class Data {

	protected $sql = "";
	protected Database $db;
	protected $data = [];
	protected $post = [];
	protected $type = "Data";
	protected $status = "";
	protected $error = "";

	/* Wie erstelle ich ein Data Objekt:
	   +   1. Aufruf Konstruktor, Übergabe der Datenbank.
	 *   2. Setzen der POST Daten.
	 *   3. Schreiben in Datenbank.
	 *   4. Bei Erfolg, setzen der Daten.
	 */

	public function __construct($db) {
		$this->db = $db;			# Set DB.
		$this->type = get_class($this);	# Get Class name.
		$this->data = [$this->default];
		Logger::debug("Data() Class " . $this->type . ", data=" . json_encode($this->data));
	}
# protected function def() { $this->data = []; } # Set Default data, redefined in every derived class.

	// ########################## Setzen von Daten ######################

	public function setPostVerify($post) {
		$mk_data = $this->setPost($post);
		$this->verify($mk_data);
		return $this->data;
	}

	abstract public function setPost($post);
	abstract public function verify($mk_data);
# abstract public function setPost($post) {
# Logger::error($this->type . "::Data::setPost() - Must be derived!");
# return $this->data;
# }
# abstract public function verify($mk_data) {
# 	$this->data = $mk_data;
# }

	public function set($post) {
		$this->post = $post;
	}

	public function setData($data) {
		$this->data = $data;
	}
	public function getData() {
		return $this->data;
	}

	// ########################### Datenbank Operationen #######################

	public function update() {
		$ret = null;
		try { 
			$sql = $this->sql_update();
			$ret = $this->db->query($sql);
			# $ret_string = implode(" ", $ret[0]);
			$this->data = array_merge($this->data, $this->post);
			$msg = "OK '" . $this . "' aktualisiert!"; #  ret=" . $ret_string;
			Logger::info($this->type . "::Data::update() " . $msg);
			Logger::debug_array($this->type . "::Data::update()", $ret);
		}
		catch(Exception $e) {
			$msg = "NOK '" . $this->data['id'] . "' nicht aktualisiert! " . $e->getMessage();
			Logger::error($this->type . "::Data::update() " . $msg);
		}
		$this->status = $msg;
		return $ret;
	}

	/*
	 * Vorgehen für SQL Operationen:
	 *  1. sql_insert() - SQL Statement aus Objekt "MkKunde" holen.
	 *  2. query()      - Datenbankaufruf.
	 *  3. $this->data  - Setzen von Daten.
	 *  4. $this->error - Meldung generieren mit "OK" oder "NOK".
	 *  5. $ret         - Rückgabe des Return Wertes: SQL-Rückgabe.
	 */
	public function write() {
		$ret = null;
		try { 
			$sql = $this->sql_insert();
			$ret = $this->db->query($sql);
			$ret_string = implode(" ", $ret[0]);
			$this->data = $this->post;
			$this->data['id'] = $ret[0]['id'];
			$msg = "OK '" . $this . "' eingerichtet!";
			Logger::info($this->type . "::Data::write() " . $msg);
			Logger::debug_array($this->type . "::Data::write()", $ret);
		}
		catch(Exception $e) {
			$msg = "NOK '" . $this . "' nicht eingerichtet!";
			$this->error = $e->getMessage();
			Logger::error($this->type . "::Data::write() " . $msg . " " . $this->error);
		}
		$this->status = $msg;
		return $ret;
	}

	/* =========================== delete() ======================
	   delete() löscht das Mk-Objekt.

	   OK - Wenn erfolgreicht:
	   - die Daten werden auf $default gesetzt.

	   NOK - Wenn Fehlerfall:
	   - Fängt Exception und setzt den 'error' Text.

	 */
	public function delete() {
		$ret = null;
		$msg = "";
		if($this->post['id'] === 0) {
			$msg = "NOK '" . $this . "' nicht vorhanden!";
			Logger::info($this->type . "::Data::delete() " . $msg);
			$this->status = $msg;
			return $ret;
		}
		try{ 
			$sql = $this->sql_delete(); 
			$ret = $this->db->query($sql);
			$msg = "OK '" . $this . "' gelöscht!";
			$this->data = [$this->default];
			Logger::info($this->type . "::Data::delete() " . $msg);
			Logger::debug_array($this->type . "::Data::delete()", $ret);
		}
		catch(Exception $e) {
			$msg = "NOK '" . $this . "' nicht gelöscht!";
			$this->error = $e->getMessage();
			Logger::error($this->type . "::Data::delete() " . $msg . " " . $this->error);
		}
		$this->status = $msg;
		return $ret;
	}

	/* getAll() - Liest mehrere Datensätze aus der Datenbank.
	 */
	public function getAll($id = 0, $type = "None") {
		Logger::debug($this->type . "::Data::getAll() Id(" . gettype($id) . ")=" . $id);
		$array = [];
		if(isset($id)) {
			try {
				if(gettype($id) === "string" and $id !== "") { 	// Gedacht für MkKunde mit Kundennummer als String.
					$sql = $this->sql_select($id,$type); 
					$array = $this->db->query($sql);
					if(isset($array)) { 
						$this->data = $array; 
						$msg = "OK '" . $this . "' gelesen!";
					}
					else { 
						$array = [$this->data]; 
						$msg = "OK '" . $this . "' nicht vorhanden!"; 
					}
				} 
				else if(gettype($id) === "integer" and $id !== 0) { // Gedacht für alle anderen Objekte.
					$sql = $this->sql_select($id,$type); 
					$array = $this->db->query($sql);
					if(isset($array)) {
						$this->data = $array;
						$msg = "OK '" . $this . "' gelesen!";
					} 
					else { 
						$array = [$this->data]; 
						$msg = "OK '" . $this . "' nicht vorhanden!"; 
					}
				} 
				else { 
					$array = [$this->data]; 
					$msg = "NOK (2) Keine Id vorhanden!";
				}

				} catch(Exception $e) {
					$msg = "NOK '" . $id . "' nicht gelesen!";
					$this->error = $e->getMessage();
					Logger::error($this->type . "::Data::get() " . $msg . " " . $this->error);
				}
			$this->status = $msg;
			}
		else { 
			$msg = "NOK (1) Keine Id vorhanden!";
			$this->status = $msg;
			$array = [$this->data]; 
		}
		Logger::debug($this->type . "::Data::getAll() Msg='" . $msg . "'");
		Logger::debug_array($this->type . "::Data::getAll() Return Data",$array);
		return $array;
	}

	/* get() - Liest nur einen Datensatz aus der Datenbank.
	 */
	public function get($id = 0, $type = "None") {
		Logger::debug($this->type . "::Data::get() Id(" . gettype($id) . ")=" . $id);
		$array = [];
		if(isset($id)) {
# Logger::debug($this->type . "::Data::get() Set Id=".($id||""));
			try {
				if(gettype($id) === "string" and $id !== "") { 	// Gedacht für MkKunde mit Kundennummer als String.
					$sql = $this->sql_select($id,$type); 
					$array = $this->db->query($sql);
					if(isset($array[0])) { 
						$this->data = $array[0]; 
						$msg = "OK '" . $this . "' gelesen!";
					}
					else { 
						$msg = "OK - '" . $this . "' nicht vorhanden!";
					}
				} 
				else if(gettype($id) === "integer" and $id !== 0) { // Gedacht für alle anderen Objekte.
					$sql = $this->sql_select($id,$type); 
					$array = $this->db->query($sql);
					$this->data = $array[0];
					$msg = "OK '" . $this . "' gelesen!";
				} 
				else { 
					$array = $this->data; 
					$msg = "NOK - Lesen '".$this->type."' nicht möglich, Keine Id vorhanden!";
				}

			} catch(Exception $e) {
				$msg = "NOK '" . $id . "' nicht gelesen!";
				$this->error = $e->getMessage();
				Logger::error($this->type . "::Data::get() " . $msg . " " . $this->error);
			}
			$this->status = $msg;
			}
		else { 
			$this->status = "NOK (1) Keine Id vorhanden!";
			$array = $this->data; 
		}
		Logger::debug_array($this->type . "::Data::get() Return Data",$array);
		return $array;
	}

	// ####################### Get Information ######################

	public function getId() { 
		if(isset($this->data['id'])) {
			Logger::debug($this->type . "::Data::getId() Id=". $this->data['id']);
			return $this->data['id']; 
		}
		else { return null; }
	}

	public function getErr() {
		if(isset($this->error)) { 
			Logger::debug($this->type . "::Data::getErr() Error=". $this->error);
			return $this->error; }
		Logger::debug($this->type . "::Data::getErr() Error=LEER");
		return "";
	}
	public function getMsg() {
		if(isset($this->status)) { 
			Logger::debug($this->type . "::Data::getMsg() Message=". $this->status);
			return $this->status; }
		Logger::debug($this->type . "::Data::getMsg() Message=LEER");
		return "";
	}
	public function success(): bool {
		$msg = $this->getMsg();
		if(str_starts_with($msg, "OK")) { 
			Logger::debug($this->type . "::Data::success() Success=true");
			return true; }
		Logger::debug($this->type . "::Data::success() Success=false");
		return false;
	}
	public function __toString() { 
		return $this->to_string(); }

	public function toString() {
		$str = get_class($this) . ", data=" . implode(",", $this->data);
		Logger::debug($this->type . "::Data::toString() Obj=" . $str);
		return $str;
	}

	// ######################## HELPER METHODS #############################
	function convertDate($date_in) {
		$datum = '';
		if(isset($date_in)) {
			if(str_contains($date_in, ".")) {
				$e = explode(".", $date_in);
				$datum = "$e[2]-$e[1]-$e[0]";
			}
			else if(str_contains($date_in, "-")) {
				$datum = $date_in;
			}
			else {
				$error = "Falsches Datumsformat, entweder 'y-m-d' oder 'd.m.y'!";
				return [false,$error];
			}
		}
		else {
			$datum = date("y-m-d");
		}
		return [true,$datum];
	}
}
