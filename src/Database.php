<?php
class Database {

	protected $name;
	protected $user;
	protected $pass;
	protected $pdo;

	public function __construct($name,$user,$pass) {
		$host = constant('DB_HOST');
		try {
			$this->name = $name;
			$this->user = $user;
			$this->pass = $pass;
			$this->pdo = new PDO("mysql:host=$host;dbname=$name;charset=utf8", $user, $pass);
			$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		} catch (PDOException $e) {
			$message = "DB Fehler: " . $e->getMessage();
			Logger::db("Database() " . $message);
			throw new PDOException($message);
		}
	}

	public function __toString() {
		return "DB " . $this->name . ", login=" . $this->user;
	}

	function query($sql) {
		Logger::db("Database::query() SQL - $sql");
		try {
			$stmt = $this->pdo->prepare($sql);
			$stmt->execute();
			$ret = $stmt->fetchAll(PDO::FETCH_ASSOC);
			Logger::db_result("Database::query() Result", $ret);
			return $ret;
		} catch (PDOException $e) {
			$message = "DB Fehler: " . $e->getMessage();
			$trace   = Logger::getTrace();
			Logger::db("Database::query(): Error - " . $message);
			Logger::error("Database::query(): " . $trace);
			throw $e;
			return $message;
		}
	}
	function query_info($ret) {
		$str1 = count($ret) . "(";
		foreach($ret as $key => $val) {
			$str1 .= $key . "=>" . count($val) . ",";
		}
		$str1 .= ")";
		Logger::debug("Database::query() 1: (" . $str1 . ")");
		$str2 = $this->show_array($ret);
		Logger::debug("Database::query() 2: " . $str2);
		$str3 = $this->show_array3($ret);
		Logger::debug("Database::query() 3: " . $str3);
	}

	function show_array2($aoa) {
		$str = "";
		foreach($aoa as $arr) {
			$keys = array_keys($arr);
			$str .= "\tKeys [" . implode(", ", $keys) . "]\n";
			$str .= "\tVals [" . implode(", ", $arr) . "]\n";
		}
		return $str;
	}
	function show_array3($a) {
		$type = gettype($a);
		$str = "";
		if($type === "array") { $str .= $type . "(" . count($a) . ") " . $this->treat_array($a); } 
		else { $str = $type; }
		return $str;
	}
	function treat_array($a) {
		$str = "";
		foreach($a as $key => $val) {
			$type = gettype($val);
			if($type === "array") { $str .= $type . "(" . count($val) . ") " . $this->treat_array($val); }
			else { $str .= "[" . $key . "=>" . $val . "(" . $type . ")" . "]"; }
		}
		return $str;
	}

	function show_array($mar) {
		$pre = "[";
		$i = 0;
		foreach($mar as $key1 => $val1) {
			$j = 0;
			$pre .= "[ $key1 => ";
			foreach($val1 as $key2 => $val2) {
				$pre .= "[$key2=>$val2],";
				$j++;
			}
			$pre .= "],";
			$i++;
		}
		$pre .= "]";
		return $pre;
	}
	function show($mar) {
		$pre = "<pre style=\"border:2px solid red;\">";
		$anz = count($mar);
		$pre .= "ANZ " . $anz . "<br>";
		$i = 0;
		foreach($mar as $entry) {
			$j = 0;
			foreach($entry as $key => $val) {
				$pre .= "($i,$j) Key: " . $key . ", Val: " . $val . "<br>";
				$j++;
			}
			$i++;
		}
		$pre .= "</pre>";
		return $pre;
	}

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
		}
		else {
			$datum = date("y-m-d");
		}
		return $datum;
	}

	function isName($name):bool {
		Logger::debug("Database::isName(): " . $name);
		$sql = "SELECT name FROM session WHERE name=:name";
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute([':name' => $name]);
		$data = $stmt->fetch();
		if($data) {
			$data_string = implode(',',$data);
			Logger::log("DEBUG Database::isName(): Data - " . $data_string);
			if($data[0] == "$name") {
				Logger::debug("Database::isName(): true");
				return true;
			}
			else {
				Logger::debug("Database::isName(): false");
				return false;
			}
		} 
		else {
			Logger::debug("Database::isName(): false");
			return false;
		}
	}
	function setNewName($name) {
		Logger::debug("Database::setNewName(): " . $name);
		$sql = "INSERT INTO session (name) VALUES (:name)";
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute([':name' => $name ]);
		$message = "Benutzer '$name' gespeichert!";
		return $message;
	}

	function setName($name) {
		Logger::debug("Database::setName(): " . $name);
		if(! $this->isName($name)) {
			$message = $this->setNewName($name);
		}
		else { $message = "Benutzer '" . $name . "' ist schon registriert!"; }
		return $message;
	}

	function getNames() {
		$sql = "SELECT name FROM session";
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute();
		return $stmt->fetchAll();
	}
}
