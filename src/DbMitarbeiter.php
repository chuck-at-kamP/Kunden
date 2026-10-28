<?php
class DbMitarbeiter extends Database {

	public function __construct() {
		$name = constant('DB_NAME_MA');
		$user = constant('DB_USER_MA');
		$pass = constant('DB_PASS_MA');
		parent::__construct($name,$user,$pass);
	}

	function getMitarbeiter() {
		Logger::debug("Database::getMitarbeiter(): ");
		$sql = "SELECT nachname,vorname,personalnummer FROM mk_mitarbeiter";
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute();
		return $stmt->fetchAll();
	}
}
