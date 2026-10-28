<?php
require_once __DIR__ . '/Database.php';
class DbKunden extends Database {

	private $last = "Kunden";

	public function __construct() {
		$name = constant('DB_NAME');
		$user = constant('DB_USER');
		$pass = constant('DB_PASS');
		parent::__construct($name,$user,$pass);
	}

	# getKunden() - Hole eine Liste aller Kunden aus der Datenbank.
	public function getKunden() {
		Logger::debug("DbKunden::getKunden(): ");
		$sql = "SELECT id, kundennummer, nachname,vorname FROM mk_kunde";
		$ret = $this->query($sql);
		$this->last = "Kundenliste";
		foreach($ret as $e) { $this->last .= " " . $e['kundennummer']; }
		return $ret;
	}

	# getKundendaten() - Hole alle Kundendaten aus der Datenbank.
	public function getKundendaten($nr) {
		$this->last = "Kundendaten";
		Logger::info("DbKunden::getKundendaten() Get '$nr'");

		# ======= Get mk_kunde =======
		$kunde = new MkKunde($this);
		$mk_kunde = $kunde->get($nr);
		Logger::info("DbKunden::getKundendaten() Got '$kunde'");
		$this->last .= " " . $kunde;
		$kunde_id = $mk_kunde[0]['id'];

		# ======= Get mk_person =======
		/*
		$person = new MkPerson($this,'Kunde');
		$mk_person = $person->get($kunde_id);
		Logger::info("DbKunden::getKundendaten() Got '$person'");
		$this->last .= " " . $person;
		$person_id = $mk_person['id'];
		*/

		# ======= Get mk_adresse =======
		$adr = new MkAdresse($this);
		$mk_adresse = $adr->get($kunde_id,"Kunde");
		Logger::info("DbKunden::getKundendaten() Got '$adr'");
		$this->last .= " " . $adr;

		# ======= Get mk_kontakt =======
		$kontakte = new MkKontakte($this);
		$mk_kontakt = $kontakte->get($kunde_id,"Kunde");
		Logger::info("DbKunden::getKundendaten() Got '$kontakte'");
		$this->last .= " " . $kontakte;

		# ======= Get mk_kontaktperson =======
		$kontaktperson = new MkPerson($this,'Kontaktperson');
		$mk_kontaktperson = $kontaktperson->get($kunde_id,"Kunde");
		Logger::info("DbKunden::getKundendaten() Got ### '$kontaktperson'");
		Logger::debug_array("DbKunden::getKundendaten() ### Kontaktperson Array:", $mk_kontaktperson);
		$this->last .= " " . $kontaktperson;
		foreach($mk_kontaktperson as $key_kpa => $kpa) {
			$kontaktperson_id = $kpa['id'];
			Logger::info("DbKunden::getKundendaten() ### Kontaktperson Id: $kontaktperson_id, Key $key_kpa" );
			$k4kp = new MkKontakte($this);
			$kontakt4kp = $k4kp->get($kontaktperson_id,"Person");
			Logger::info("DbKunden::getKundendaten() ### Kontakt: $k4kp" );
			Logger::debug_array("DbKunden::getKundendaten() ### Kontakt Array:", $kontakt4kp);
			$mk_kontaktperson[$key_kpa]['kontakte'] = $kontakt4kp;
		}
		Logger::debug_array("DbKunden::getKundendaten() ### Kontaktperson Array:", $mk_kontaktperson);

		# ======= Get mk_tafel =======
		$tafel = new MkTafel($this);
		$mk_tafel = $tafel->get($kunde_id);
		Logger::info("DbKunden::getKundendaten() Got '$tafel'");
		$this->last .= " " . $tafel;

		return [$mk_kunde,$mk_adresse,$mk_kontakt,$mk_kontaktperson,$mk_tafel];
	}
	# last() - Get Result of Last operation, similar to __toString().
	public function last() {
		return $this->last;
	}
	# __toString() - String representation of object.
	public function __toString() {
		return constant('DB_NAME');
	}

	public function setPost($action, $post) {
		# =============== Set Kunde =================
		# Set Kaufhaus
		if($action === "Select_Kaufhaus" and isset($post['kaufhaus'])) { $mk_kunde['kaufhaus'] = true; }
		else { 
			if(isset($post['kaufhaus']) and $post['kaufhaus'] === "Kaufhaus") { $mk_kunde['kaufhaus'] = true; } 
			else { $mk_kunde['kaufhaus'] = false; }
		}
		# Set Tafel
		if($action === "Select_Tafel" and isset($post['tafel'])) { $mk_kunde['tafel'] = true; }
		else { 
			if(isset($post['tafel']) and $post['tafel'] === "Tafel") { $mk_kunde['tafel'] = true; } 
			else { $mk_kunde['tafel'] = false; }
		}
		if(isset($post['kid'])) { $mk_kunde['id'] = $post['kid']; } else { $mk_kunde['id'] = 0; }
		if(isset($post['kundennummer'])) { $mk_kunde['kundennummer'] = $post['kundennummer']; } else { $mk_kunde['kundennummer'] = ""; }
		if(isset($post['nachweis'])) { $mk_kunde['nachweis'] = $post['nachweis']; } else { $mk_kunde['nachweis'] = ""; }
		if(isset($post['gueltigkeit'])) { $mk_kunde['gueltigkeit'] = $post['gueltigkeit']; } else { $mk_kunde['gueltigkeit'] = ""; }
		if(isset($post['bemerkung'])) { $mk_kunde['bemerkung'] = $post['bemerkung']; } else { $mk_kunde['bemerkung'] = ""; }

		# ============= Set Person =============
		if(isset($post['pid'])) { $mk_person['id'] = $post['pid']; } else { $mk_person['id'] = 0; }
		if(isset($post['p_type'])) { $mk_person['p_type'] = $post['p_type']; } else { $mk_person['p_type'] = "Kunde"; }
		if(isset($post['kunde_id'])) { $mk_person['kunde_id'] = $post['kunde_id']; } else { $mk_person['kunde_id'] = 0; }
		if(isset($post['nachname'])) { $mk_person['nachname'] = $post['nachname']; } else { $mk_person['nachname'] = ""; }
		if(isset($post['vorname'])) { $mk_person['vorname'] = $post['vorname']; } else { $mk_person['vorname'] = ""; }

		# ============= Set Adresse ==============
		if(isset($post['aid'])) { $mk_adresse['id'] = $post['aid']; } else { $mk_adresse['id'] = 0; }
		if(isset($post['person_id'])) { $mk_adresse['person_id'] = $post['person_id']; } else { $mk_adresse['person_id'] = 0; }
		if(isset($post['strasse'])) { $mk_adresse['strasse'] = $post['strasse']; } else { $mk_adresse['strasse'] = ""; }
		if(isset($post['hausnummer'])) { $mk_adresse['hausnummer'] = $post['hausnummer']; } else { $mk_adresse['hausnummer'] = ""; }
		if(isset($post['plz'])) { $mk_adresse['plz'] = $post['plz']; } else { $mk_adresse['plz'] = ""; }
		if(isset($post['ort'])) { $mk_adresse['ort'] = $post['ort']; } else { $mk_adresse['ort'] = ""; }

		# ============= Set Tafel ==============
		if(isset($post['tid'])) { $mk_tafel['id'] = $post['tid']; } else { $mk_tafel['id'] = 0; }
		if(isset($post['kunde_id_tafel'])) { $mk_tafel['kunde_id'] = $post['kunde_id_tafel']; } else { $mk_tafel['kunde_id'] = 0; }
		if(isset($post['personen'])) { $mk_tafel['personen'] = $post['personen']; } else { $mk_tafel['personen'] = 0; }
		if(isset($post['preis'])) { $mk_tafel['preis'] = $post['preis']; } else { $mk_tafel['preis'] = 0; }
		if(isset($post['gruppe'])) { $mk_tafel['gruppe'] = $post['gruppe']; } else { $mk_tafel['gruppe'] = ""; }

		# ============= Set Kontakt ==============
		$mk_kontakt = [];
		for($i=0; $i<10;$i++) {
			$tmp = [];
			if(isset($post["kontakt_type_$i"]) and isset($post["kontakt_$i"])) { 
				$mk_kontakt[$i]['k_type'] = $post["kontakt_type_$i"];
				$mk_kontakt[$i]['kontakt'] = $post["kontakt_$i"];
				if(isset($post["ktid_$i"])) { $mk_kontakt[$i]['id'] = $post["ktid_$i"]; } else { $mk_kontakt[$i]['id'] = 0; }
				if(isset($post["person_id_kontakt_$i"])) { 
					$mk_kontakt[$i]['person_id'] = $post["person_id_kontakt_$i"]; } else { $mk_kontakt[$i]['person_id'] = 0; 
				}
			}
			else if(!isset($post["kontakt_type_$i"]) and !isset($post["kontakt_$i"])) { 
				if($i == 0) {
					$mk_kontakt[$i]['kontakt_type'] = '';
					$mk_kontakt[$i]['kontakt'] = '';
					$mk_kontakt[$i]['id'] = 0;
					$mk_kontakt[$i]['person_id'] = 0;
				}
				break;
			}
			else {
				$str = "Kontakt $i not set! ";
				if(isset($post["kontakt_type_$i"])) { $str .= "kontakt_type=" . $post["kontakt_type_$i"]; }
				if(isset($post["kontakt_$i"])) { $str .= ", kontakt=" . $post["kontakt_$i"]; }
				Logger::error("DbKunden::setPost() " . $str);
			}
		}

		# ============= usw. ==============
		return [$mk_kunde,$mk_person,$mk_adresse,$mk_tafel,$mk_kontakt];
	}
	# ==================================== Überarbeiten =====================================
	public function setNeuerKunde($a) {
		try {
			# $mk_kunde = new MkKunde($a['id'],$a['nachweis'],$a['valid'],$a['tafel'],$a['kaufhaus'],$this);
		} catch(Exception $e) {
			return $e->getMessage();
		}
	}

	public function setNeuerKunde1($arr) {

		# ======= Kunde in mk_kunde schreiben ===========
		$knr = $arr['id'];
		if(isset($arr['tafel']) and $arr['tafel'] === "Tafel") { $t = "true"; } else { $t = "false"; }
		if(isset($arr['moebel']) and $arr['moebel'] === "Kaufhaus") { $k = "true"; } else { $k = "false"; }
		$n = $arr['nachweis'];
		$g = $this->convertDate($arr['valid']);
		$b = $arr['bemerkung'];
		$sql1 = "INSERT INTO `mk_kunde` (`kundennummer`,`tafel`,`kaufhaus`,`nachweis`,`gueltigkeit`,`bemerkung`) VALUES ('$knr', $t, $k,'$n','$g','$b')";
		$ret = $this->query($sql1);
		Logger::debug("DbKunden::setNeuerKunde - $sql1");

		# ======== Id des neuen Kunden auslesen ==============
		$sql_t1 = "SELECT k.id as kunde FROM mk_kunde k where k.kundennummer='$knr'";
		$ids = $this->query($sql_t1);
		$kid = $ids[0]['kunde'];

		# ======== Person in mk_person schreiben =============
		$nam = $arr['name'];
		$vor = $arr['vorname'];
		$typ = "Kunde";
		$sql2 = "INSERT INTO `mk_person` (`kunde_id`, `p_type`, `nachname`, `vorname`) VALUES ($kid, '$typ', '$nam', '$vor');";
		$ret = $this->query($sql2);
		Logger::debug("DbKunden::setNeuerKunde - $sql2");
		# ======== Id der Person auslesen ==============
		$sql_t2 = "SELECT p.id as person FROM mk_person p where p.kunde_id='$kid';";
		$ids = $this->query($sql_t2);
		$pid = $ids[0]['person'];

		# ======== Adresse in mk_adresse schreiben =========
		$str = $arr['strasse'];
		$hnr = $arr['hausnummer'];
		$plz = $arr['plz'];
		$ort = $arr['ort'];
		$sql3 = "INSERT INTO `mk_adresse` (`person_id`, `strasse`, `hausnummer`, `plz`, `ort`) VALUES ($pid, '$str', '$hnr', '$plz', '$ort');";
		$ret = $this->query($sql3);
		Logger::debug("DbKunden::setNeuerKunde - $sql3");

		# ======== Kontakt in mk_kontakt ===============
		for($i=0; $i<=9; $i++) {
		if(isset($arr["kontakt_$i"]) and isset($arr["kontakt_type_$i"])) {
			$ktyp = $arr["kontakt_type_$i"];
			$kon = $arr["kontakt_$i"];
			$sql4 = "INSERT INTO `mk_kontakt` (`person_id`, `k_type`, `kontakt`) VALUES ($pid, '$ktyp', '$kon');";
			$ret = $this->query($sql4);
			Logger::debug("DbKunden::setNeuerKunde - $sql4");
		}
		}

		# ======== Tafel in mk_tafel ===============
		$per = $arr['personen'];
		$prs = $arr['preis'];
		$grp = $arr['gruppe'];
		$sql5 = "INSERT INTO `mk_tafel` (`kunde_id`, `personen`, `preis`, `gruppe`) VALUES ($kid, $per, $prs, '$grp');";
		$ret = $this->query($sql5);
		Logger::debug("DbKunden::setNeuerKunde - $sql5");
	}

	public function setKunde($arr) {
		if($_POST["kunden"]) {
			$kunde = explode("|", $_POST["kunden"]);
 			$nr = $kunde[2];
			$sql_t1 = "SELECT k.id as kunde, p.id as person, a.id as adress FROM mk_kunde k, mk_person p, mk_adresse a where k.kundennummer='$nr' and p.kunde_id=k.id and a.person_id=p.id";
			$ids = $this->query($sql_t1);
			$kid = $ids[0]['kunde'];
			$pid = $ids[0]['person'];
			$aid = $ids[0]['adress'];
		}

		$t = $arr['tafel'];
		$k = $arr['moebel'];
		$n = $arr['nachweis'];
		$g = $arr['valid'];
		$b = $arr['bemerkung'];
		$sql1 = "UPDATE mk_kunde SET tafel='$t', kaufhaus='$k', nachweis='$n', gueltigkeit='$g', bemerkung='$b' WHERE id='$kid'";
		# $ret = $this->query($sql1);
		Logger::debug("DbKunden::setKunde - $sql1");

		$nam = $arr['name'];
		$vor = $arr['vorname'];
		$typ = "Kunde";
		$sql2 = "UPDATE mk_person SET p_type='$typ', vorname='$vor', nachname='$nam' WHERE id='$pid'";
		# $ret = $this->query($sql2);
		Logger::debug("DbKunden::setKunde - $sql2");

		$str = $arr['strasse'];
		$hnr = $arr['hausnummer'];
		$plz = $arr['plz'];
		$ort = $arr['ort'];
		$sql3 = "UPDATE mk_adresse SET strasse='$str', hausnummer='$hnr', plz='$plz', ort='$ort' WHERE id='$aid'";
		# $ret = $this->query($sql3);
		Logger::debug("DbKunden::setKunde - $sql3");
	}

	function getKunde($nr) {
		Logger::debug("Database::getKunde(): ");
		# $sql = "SELECT mk_person.nachname,mk_person.vorname,mk_kunde.kundennummer FROM mk_kunde JOIN mk_person on mk_kunde.id = mk_person.kunde_id WHERE mk_kunde.kundennummer=" . $nr;
		# $sql = "SELECT k.kundennummer, k.changed, p.vorname, p.nachname FROM mk_kunde k JOIN mk_person p ON k.id = p.kunde_id WHERE k.kundennummer='$nr'";
		$sql = "select k.*, p.*, a.*, m.*, t.* from mk_kunde k LEFT JOIN mk_person p on k.id = p.kunde_id JOIN mk_adresse a on p.id = a.person_id LEFT JOIN mk_kontakt m on p.id = m.person_id JOIN mk_tafel t ON k.id = t.kunde_id where k.kundennummer='$nr' GROUP BY k.id;";
		try {
			$stmt = $this->pdo->prepare($sql);
			$stmt->execute();
		} catch (PDOException $e) {
			$message = "DB Fehler: " . $e->getMessage();
			return $message;
		}
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	function getKunde1($nr) {
		Logger::debug("Database::getKunde1(): ");
		$sql = "select k.*, p.*, a.*, t.* from mk_kunde k LEFT JOIN mk_person p on k.id = p.kunde_id JOIN mk_adresse a on p.id = a.person_id LEFT JOIN mk_tafel t ON k.id = t.kunde_id where k.kundennummer='$nr' AND p.p_type='Kunde'";
		return $this->query($sql);
	}
	function getKontakt($nr) {
		Logger::debug("Database::getKontakt(): ");
		$sql = "select k.id, p.id,  m.* from mk_kunde k LEFT JOIN mk_person p on k.id = p.kunde_id LEFT JOIN mk_kontakt m ON p.id = m.person_id WHERE k.kundennummer='$nr' AND p.p_type='Kunde'";
		return $this->query($sql);
	}
	function getKontakt4Person($id) {
		Logger::debug("Database::getKontakt4Pers(): ");
		$sql = "select p.id,  m.* from mk_person p LEFT JOIN mk_kontakt m ON p.id = m.person_id WHERE p.id='$id'";
		return $this->query($sql);
	}

	function getKontaktperson($nr) {
		Logger::debug("Database::getKontaktperson(): ");
		$sql = "select k.id, p.* from mk_kunde k JOIN mk_person p on k.id = p.kunde_id where k.kundennummer='$nr' AND p.p_type='Kontaktperson'";
		return $this->query($sql);
	}

	function assign_data($kd) {
		$nr = count($kd);
		Logger::debug("Database::assign_data(): Anz=" . $nr);
		$data = [
			'kundennummer' => $kd[2], # mk_kunde
			'tafel'     => $kd[3],
			'kaufhaus'  => $kd[4],
			'nachweis'  => $kd[5],
			'validity'  => $kd[6],
			'bemerkung' => $kd[7],
		
			'person_type' => $kd[10], # mk_person
			'vorname'   => $kd[11],
			'nachname'  => $kd[12],

			'strasse'   => $kd[14],		# mk_adresse
			'hausnummer' => $kd[15],
			'plz'       => $kd[16],
			'ort'       => $kd[17],

			'kontakt_type' => $kd[18], # mk_kontakt
			'kontakt'   => $kd[19],

		    'personen'  => $kd[20],		# mk_tafel
		    'preis'     => $kd[21],
		    'gruppe'    => $kd[22],
		];
		return $kd;
	}

}
