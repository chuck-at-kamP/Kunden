<?php
require_once __DIR__ . '/DbKunden.php';
require_once __DIR__ . '/Database.php';
class App {
	protected $db;
	protected $obj = [];
	protected $post = [];
	protected $action_type = "";
	protected $action_sub_type = "";
	protected $action_gui = "";

	public function __construct() { 
		$this->db = new DbKunden();		# Initialize DB
		$this->initData();				# Initialize Data Objects.
		Logger::info("App() DB created db=" . $this->db);
	}

	public function getKundenliste() {
		$kundenliste = $this->db->getKunden();
		Logger::info("App::getKundenliste() Last operation '" . $this->db->last() . "'");
		return $kundenliste;
	}

	public function __toString() {
		return "App Kunden DB=" . $this->db;
	}

	# TBD: Delete when DB is handled only in Class "App".
	public function getDb() { return $this->db; }

	public function getAction() { return [$this->action_type,$this->action_sub_type]; }	

	# Get Actual data.
	public function get() {
		$mk_kunde = $this->obj['mk_kunde']->get(0);
		// $mk_person = $this->obj['mk_person']->get(0);
		$mk_adresse = $this->obj['mk_adresse']->get(0);
		$mk_tafel = $this->obj['mk_tafel']->get(0);
		$mk_kontakt = $this->obj['mk_kontakt']->get(0);
		return [$mk_kunde,$mk_adresse,$mk_tafel,$mk_kontakt];
	}

	# ================== POST and DATA ==================

	public function distribute($post) {
		$this->post = $post;
		[$action_type,$action_sub_type,$action_gui,$data] = $this->evaluateAction($post);
		$this->action_type = $action_type;
		$this->action_sub_type = $action_sub_type;
		$this->action_gui = $action_gui;
		$array = [];
		Logger::info("App::distribute() ====================== Beg $action_type $action_sub_type $action_gui ======================");

		if($action_type === "Select_Customer" and $action_sub_type==="Neuer_Kunde" and $action_gui==="Select_Tafel") {
			Logger::info("App::distribute() Select Customer, Neuer_Kunde, Select_Tafel.");
			$this->setPost($post);					# Setzen der Daten.
			$array = $this->getKundendaten();
		} else if($action_type === "Select_Customer" and $action_sub_type==="Neuer_Kunde" and $action_gui==="Add_Contact") {
			Logger::info("App::distribute() Select Customer, Neuer_Kunde, Add_Contact.");
			$this->setPost($post);					# Setzen der Daten.
			$array = $this->getKundendaten();
		} else if($action_type === "Select_Customer" and $action_sub_type==="Neuer_Kunde") {
			Logger::info("App::distribute() Neuer Kunde.");
			$array = $this->getKundendaten();
		} else if($action_type === "Select_Customer") {	# Returns Kundendaten if "Select_Customer" is choosen.
			$kunde = explode("|", $data);
			$nr = $kunde[1];
			Logger::info("App::distribute() Get Customer. nr=" . $nr);
			$array = $this->getKundendaten($nr);
			Logger::info("App::distribute() Got Array. count=" . count($array));
		} else if($action_type === "Create_Customer") {
			Logger::info("App::distribute() Kunde Speichern.");
			$this->setPostVerify($post);			# Setzen der Daten.
			$this->write();							# Speichern der Daten in DB.
			$array = $this->getKundendaten();		# Rückgabe der Daten zur Darstellung auf GUI.
		# } else if($action === "Select_Kaufhaus") {
		# 	$array = $this->setPost($post);
		# 	if(isset($post['kundennummer'])) { $array = $this->getKundendaten($post['kundennummer']); }
		} else if($action_type === "Delete_Customer") {
			$this->setPostVerify($post);
			$this->printAll();
			$this->delete();
			$array = $this->getKundendaten();		# Rückgabe der Daten zur Darstellung auf GUI.
		} else {
			$array = $this->setPost($post); 		# Wenn ein POST rein kommt, müssen die Daten immer gesetzt werden!
		}
		/*
		$array = $this->get();
		if($action === "Select_Tafel") {
			[$mk_kunde,$mk_person,$mk_adresse,$mk_tafel,$mk_kontakt] = $APP->get();
		}
		else if($action !== "Select_Customer") {
			[$mk_kunde,$mk_person,$mk_adresse,$mk_tafel,$mk_kontakt] = $APP->setPost($action, $_POST);
		}
		*/
		$this->printAll();
		Logger::info("App::distribute() ====================== End $action_type $action_sub_type $action_gui ======================");
		return $array;
	}

	public function set($mk_kunde,$mk_person,$mk_adresse,$mk_tafel,$mk_kontakt) {
		[$k,$p,$a,$t,$o] = $this->abbreviate();
		$k->set($mk_kunde);
		$p->set($mk_person);
		$a->set($mk_adresse);
		$t->set($mk_tafel);
		$o->set($mk_kontakt);
	}

	# TBD: Löschen, wenn die Daten aus den einzelnen Klassen (MkKunde, MkPerson, ...) extrahiert werden.
	public function setPost($post) {
		$mk_kunde   = $this->obj["mk_kunde"]->setPost($post);
		$mk_person  = $this->obj["mk_person"]->setPost($post);
		$mk_adresse = $this->obj["mk_adresse"]->setPost($post);
		$mk_tafel   = $this->obj["mk_tafel"]->setPost($post);
		$mk_kontakt = $this->obj["mk_kontakt"]->setPost($post);
		return [$mk_kunde,$mk_person,$mk_adresse,$mk_tafel,$mk_kontakt]; 
	}
	public function setPostVerify($post) {
		$mk_kunde   = $this->obj["mk_kunde"]->setPostVerify($post);
		$mk_person  = $this->obj["mk_person"]->setPostVerify($post);
		$mk_adresse = $this->obj["mk_adresse"]->setPostVerify($post);
		$mk_tafel   = $this->obj["mk_tafel"]->setPostVerify($post);
		$mk_kontakt = $this->obj["mk_kontakt"]->setPostVerify($post);
		return [$mk_kunde,$mk_person,$mk_adresse,$mk_tafel,$mk_kontakt]; 
	}

	public function write() {
		$ret = $this->obj["mk_kunde"]->write();
		if(isset($ret[0]['kundennummer']) and isset($ret[0]['id'])) {
			Logger::info("App::write() Kunde '" . $ret[0]['kundennummer'] . " (" . $ret[0]['id'] . ") erfolgreich in DB.");

			$kunde_id = $ret[0]['id'];
			$this->obj['mk_person']->set_id($kunde_id);
			$ret = $this->obj["mk_person"]->write();
			Logger::debug_array("App::write() Person ", $ret);
			Logger::info("App::write() Person '" . $ret[0]['nachname'] . " (" . $ret[0]['id'] . "/" . $ret[0]['kunde_id'] . ") erfolgreich in DB.");
	
			$person_id = $ret[0]['id'];
			$this->obj['mk_adresse']->set_id($person_id);
			$ret = $this->obj["mk_adresse"]->write();
			Logger::info("App::write() Adresse '" . $ret[0]['plz'] . " (" . $ret[0]['id'] . "/" . $ret[0]['person_id'] . ") erfolgreich in DB.");

			$this->obj['mk_tafel']->set_id($kunde_id);
			$ret = $this->obj["mk_tafel"]->write();
			Logger::debug_array("App::write() Tafel ", $ret);
			Logger::info("App::write() Tafel '" . $ret[0]['gruppe'] . " (" . $ret[0]['id'] . "/" . $ret[0]['kunde_id'] . ") erfolgreich in DB.");

			Logger::debug("App::write() Kontakt1 " . $this->obj['mk_kontakt']);
			$this->obj['mk_kontakt']->set_id($person_id);
			$ret = $this->obj["mk_kontakt"]->write();
			Logger::debug_array("App::write() Kontakt2 ", $ret);
			if(isset($ret[0])) {
				Logger::info("App::write() Kontakt3 '" . $ret[0]['kontakt'] . " (" . $ret[0]['id']. "/" . $ret[0]['person_id'] . ") erfolgreich in DB.");
			}
		} else {
			Logger::info("App::write() Kunde nicht eingerichtet in DB.");
		}
	}

	/* Löschen:
		1. Setzen der Ids. Geschieht in setPostVerify()
		2. Löschen aller Objekte. Hier in delete()
		3. Sammeln und darstellen der Meldungen.
	*/
	public function delete() {
		$ret = $this->obj["mk_kontakt"]->delete();
		$ret = $this->obj["mk_tafel"]->delete();
		$ret = $this->obj["mk_adresse"]->delete();
		$ret = $this->obj["mk_person"]->delete();
		$ret = $this->obj["mk_kunde"]->delete();
	}

	public function getMsg() {
		$msg = [];
		array_push($msg, $this->obj["mk_kunde"]->getMsg());
		array_push($msg, $this->obj["mk_person"]->getMsg());
		array_push($msg, $this->obj["mk_adresse"]->getMsg());
		array_push($msg, $this->obj["mk_tafel"]->getMsg());
		foreach($this->obj["mk_kontakt"]->getMsg() as $tmp) { array_push($msg, $tmp); }
		return $msg;
	}

	public function printAll() {
		Logger::debug("App::printAll() -------------------------------------");
		Logger::debug_array("App::printAll() Kunde",$this->obj['mk_kunde']->get(""));
		Logger::debug_array("App::printAll() Person",$this->obj['mk_person']->get(0));
		Logger::debug_array("App::printAll() Adresse",$this->obj['mk_adresse']->get(0));
		Logger::debug_array("App::printAll() Tafel",$this->obj['mk_tafel']->get(0));
		Logger::debug_array("App::printAll() Kontakt",$this->obj['mk_kontakt']->get(0));
		Logger::debug("App::printAll() -------------------------------------");
	}

	public function getKundendaten($nr = "") {
		Logger::debug("App::getKundendaten() Get Kunde '$nr'");
		$mk_kunde   = $this->obj['mk_kunde']->get($nr); 			# ======= Get mk_kunde =======
		$kunde_id   = $this->obj['mk_kunde']->getId(); 				# ======= Get Kunde Id =======
		// $kunde_id   = $mk_kunde['id'];
		// Logger::debug("App::getKundendaten() Get Person for Kunde '$kunde_id'");
		// $mk_person  = $this->obj['mk_person']->get($kunde_id); 		# ======= Get mk_person =======
		// if(isset($mk_person['id'])) $person_id  = $mk_person['id']; else $person_id = 0;

		Logger::debug("App::getKundendaten() Get Adresse for Kunde '$kunde_id'");
		$mk_adresse = $this->obj['mk_adresse']->get($kunde_id); 	# ======= Get mk_adresse =======
		Logger::debug("App::getKundendaten() Get Kontakte for Kunde '$kunde_id'");
		$mk_kontakt = $this->obj['mk_kontakt']->get($kunde_id); 	# ======= Get mk_kontakt =======
		Logger::debug("App::getKundendaten() Get Tafel for Kunde '$kunde_id'");
		$mk_tafel   = $this->obj['mk_tafel']->get($kunde_id); 		# ======= Get mk_tafel =======

		$mk_kontaktperson = [];
		# ======= Get mk_kontaktperson =======
		/*
		$kontaktperson = new MkPerson($this,'Kontaktperson');
		$mk_kontaktperson = $this->obj['mk_kunde']->get($kunde_id);
		Logger::info("App::getKundendaten() Got ### '$kontaktperson'");
		Logger::debug_array("App::getKundendaten() ### Kontaktperson Array:", $mk_kontaktperson);
		$this->last .= " " . $kontaktperson;
		foreach($mk_kontaktperson as $key_kpa => $kpa) {
			$kontaktperson_id = $kpa['id'];
			Logger::info("App::getKundendaten() ### Kontaktperson Id: $kontaktperson_id, Key $key_kpa" );
			$k4kp = new MkKontakt($this);
			$kontakt4kp = $this->obj['mk_kunde']->get($kontaktperson_id);
			Logger::info("App::getKundendaten() ### Kontakt: $k4kp" );
			Logger::debug_array("App::getKundendaten() ### Kontakt Array:", $kontakt4kp);
			$mk_kontaktperson[$key_kpa]['kontakte'] = $kontakt4kp;
		}
		Logger::debug_array("App::getKundendaten() ### Kontaktperson Array:", $mk_kontaktperson);
		*/
		return [$mk_kunde,$mk_adresse,$mk_tafel,$mk_kontakt,$mk_kontaktperson];
	}

	# =============== PRIVATE Methods =====================

	private function initData() {
		$this->obj["mk_kunde"]   = new MkKunde($this->db);
		$this->obj["mk_person"]  = new MkPerson($this->db,"Kunde");
		$this->obj["mk_adresse"] = new MkAdresse($this->db);
		$this->obj["mk_tafel"]   = new MkTafel($this->db);
		$this->obj["mk_kontakt"] = new MkKontakte($this->db);
	}

	# $action = evaluateAction($post);
	#
	# ---- Datenbank ----
	# Select_Customer + kunden="<name>"           - Soll Kundendaten aus DB holen und darstellen.
	# Change_Customer + Button_Change="Speichern" - Soll Kundendaten in DB speichern (insert oder update).
	# Change_Customer + Button_Delete="Löschen"   - Soll Kundendaten aus DB löschen (delete).

	# ---- Oberfläche ----
	# Select_Customer + kunden="Neuer_Kunde" - Soll Datensätze leeren um neue Kundendaten eingeben zu können.
	# Add_Contact                            - Soll nur eine Kontakt-Zeile hinzufügen.
	# Add_Contactperson                      - Soll nur eine Kontaktperson-Zeile hinzufügen.

	private function evaluateAction($post) {
		$message = "Evaluate Action";
		if(isset($post['action_type'])) { $message .= ", action_type=" . $post['action_type']; }
		if(isset($post['action_sub_type'])) { $message .= ", action_sub_type=" . $post['action_sub_type']; }
		if(isset($post['action_gui'])) { $message .= ", action_gui=" . $post['action_gui']; }
		if(isset($post['nachname'])) { $message .= ", nachname=" . $post['nachname']; }
		if(isset($post['vorname'])) { $message .= ", vorname=" . $post['vorname']; }
		if(isset($post['kunden'])) { $message .= ", kunden=" . $post['kunden']; }
		if(isset($post['Button_Change'])) { $message .= ", Button_Change=" . $post['Button_Change']; }
		if(isset($post['Button_Delete'])) { $message .= ", Button_Delete=" . $post['Button_Delete']; }
		if(isset($post['Button_Save']))   { $message .= ", Button_Save=" . $post['Button_Save']; }
		Logger::info("App::evaluateAction() $message"); # ------------ Ausgabe der Eingangsparameter

		$action_type = "";
		$action_sub_type = "";
		$action_gui = "";
		if(isset($post['kunden'])) $data = $post['kunden'];
		if(isset($post['action_type'])) $action_type = $post['action_type'];
		if(isset($post['action_sub_type'])) $action_sub_type = $post['action_sub_type'];
		if(isset($post['action_gui'])) $action_gui = $post['action_gui'];
		if(isset($post['action_type']) and $post['action_type'] === "Select_Customer" and isset($post['kunden']) and $post['kunden'] === "Neuer_Kunde") {
			$action_type = $post['action_type'];
			$action_sub_type = $post['kunden'];
		}
		if(isset($post['Button_Change'])) { $action_type = "Change_Customer"; }
		else if(isset($post['Button_Delete'])) { $action_type = "Delete_Customer"; }
		else if(isset($post['Button_Save']) and $post['Button_Save'] === "Speichern") { $action_type = "Create_Customer"; }
		Logger::info("App::evaluateAction() Action Type '$action_type', Action Subtype '$action_sub_type', Action Gui '$action_gui'");
		return [$action_type,$action_sub_type,$action_gui,$data];
	}

	# abbreviate() - To get a short form of object instances.
	private function abbreviate() {
		$k = $this->obj["mk_kunde"];
		$p = $this->obj["mk_person"];
		$a = $this->obj["mk_adresse"];
		$t = $this->obj["mk_tafel"];
		$o = $this->obj["mk_kontakt"];
		return [$k,$p,$a,$t,$o];
	}
}


