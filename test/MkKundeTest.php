<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;
require_once __DIR__ . '/../src/MkKunde.php';
require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/Logger.php';
require_once __DIR__ . '/Test.php';

require_once __DIR__ . "/../ignore/config-test.php";

/*
Methoden der Klasse MkKunde:
	public function verify($mk_kunde)		- Üperprüft die eingegebenen Daten.
	public function setPost($post)			- Setzt Daten aus $_POST.
	public function __toString()			- Gibt Objekt als String zurück.

	protected function set_kundennummer($value)
	protected function set_nachweis($value)
	protected function set_gueltigkeit($value)
	protected function set_type($tafel,$kaufhaus)
	
	protected function sql_insert()
	protected function sql_update()
	protected function sql_delete()
	protected function sql_select($nr)


Methoden der abstrakten Klasse Data:

	public function setPostVerify($post)		- ruft setPost() und verify() auf.

	abstract public function setPost($post)		- Muss in abgeleiteter Klasse implementiert werden.
	abstract public function verify($mk_data)	- Muss in abgeleiteter Klasse implementiert werden.

	public function set($mk_data)			- setzt Daten.

	public function update()				- ruft sql_update() auf.
	public function write()					- ruft sql_insert() auf.
	public function delete()				- ruft sql_delete() auf.
	public function get($id = 0)			- ruft sql_select() auf.

	public function getMsg()
	public function getId()
	public function success()

	public function __toString()
	function sendDb()
	function convertDate($date_in)

Wie sieht die Schnittstelle nach aussen aus?
	$obj = new MkKunde($db);	- Konstruktor mit DB.
	$obj->set($mk_kunde);		- Setzt Daten.
	$obj->setPostVerify($post);	- Überprüft und setzt Daten.

	$obj->get($id);				- Gibt Daten zurück, liest ev. aus DB.
	$obj->write();				- Schreibt Daten in DB.
	$obj->update();				- Aktualisiert Daten in DB.
	$obj->delete();				- Löscht Daten aus DB.

	$obj->getMsg();				- "error" Feld, aktuelle Fehler oder Erfolgsmeldung, zurück.
	$obj->getId();				- "id" Feld zurück.
	$obj->success();			- true|false aus "error" ("OK","") Feld.
*/


final class MkKundeTest extends TestCase
{
	public static $db_static;
	public static $obj_static;
	public static $test_static;

	public static $id = 0;

	// Einmaliges Setup, wegen "static" müssen auch die Attribute "static" sein.
	public static function setUpBeforeClass(): void {				
		print "===============================================\n";
		print "Test Setup Global ...\n";
		$name = DB_NAME;
		$user = DB_USER;
		$pass = DB_PASS;
		print "  - Setup Database ...\n";
		MkKundeTest::$db_static  = new Database($name, $user, $pass);
		print "  - Setup MkKunde ...\n";
		MkKundeTest::$obj_static = new MkKunde(MkKundeTest::$db_static);
		print "  - Setup Test Statistik ...\n";
		MkKundeTest::$test_static = new Test("MkKundeTest");
		print "Test Setup Global finished!\n";
		print "===============================================\n";
		print "Test running:\n";
	}

	public function setUp(): void {  
		error_reporting(E_ALL);			// Muss vor jeden Test, sonst wird keine Warning angezeigt.
	}
	public function tearDown(): void {  }

	private function getObjects() {		// Um eine Kurzschreibweise der Variablen zu haben.
		$db = MkKundeTest::$db_static;
		$obj = MkKundeTest::$obj_static;
		$test = MkKundeTest::$test_static;
		return [$db,$obj,$test];
	}

	private function result_output($testName,$testClass,$text) {
		$class = get_class($this);
		$obj   = $class::$obj_static;
		$test  = $class::$test_static;
		$id  = $obj->getId();
		$str = $obj->__toString();
		$msg = $obj->getMsg();
		$success = $obj->success();
		$test->bet($testName,$testClass . " " . $text . " " . $str . ", Id=" . $id . ", Success=" . $success . ", Msg=" . $msg);
		if(!$success) { 
			$error = $obj->getErr();
			$test->bet($testName,$testClass . " " . $text . " " . $str . " " . $error);
		}
		return [$id,$str,$msg,$success];
	}

	// ######################## TESTS ############################

    public function testConstructor(): void {
		[$db,$obj,$test] = $this->getObjects();
		$test->add("testConstructor", "MkKunde() Create new Object.");			// Erzeugung einer Testausgabe am Anfang des Tests
		$str = $obj->__toString();
        $this->assertSame("Kunde NONE (0)", $str);
		$test->fin("testConstructor", "MkKunde() Object created: " . $str);		// Erzeugung einer Testausgabe am Ende des Tests
	}

    public function testConstructor1(): void {
		[$db,$obj,$test] = $this->getObjects();
		$test->add("testConstructor1", "MkKunde() Create new Object.");
		$obj1 = new MkKunde(MkKundeTest::$db_static);
        $this->assertSame("Kunde NONE (0)", $obj1->__toString());
		$str = $obj1->__toString();
		$test->fin("testConstructor1", "MkKunde() Object created: " . $str);
    }

    public function testWrite(): void {
		[$db,$obj,$test] = $this->getObjects();
		$post = ['kundennummer'=>'010/26', 'vorname'=>'Hans', 'nachname'=>'Hansen', 'tafel'=>'Tafel', 'kaufhaus'=>'Kaufhaus', 
			'nachweis'=>'ALG', 'gueltigkeit'=>'2026-12-31', 'bemerkung'=>'Unit Test'];
		$test->add("testWrite", "MkKunde::write() In: ", $post);
		$obj->set($post);
		$ret = $obj->write();
		$test->bet("testWrite","MkKunde::write() Returns: ", $ret);
		[$id,$str,$msg,$success] = $this->result_output("testWrite","MkKunde","Create");
		$test->fin("testWrite", "MkKunde::write() Out: " . $str . ", msg=" . $msg, $ret);

		$this->assertSame('[{"id":' . $id . ',"kundennummer":"010\/26"}]', json_encode($ret));
        $this->assertSame("Kunde 010/26 ($id)", $str);
		$this->assertSame("OK 'Kunde 010/26 (" . $id. ")' eingerichtet!", $msg);
		$this->assertTrue($success);
    }
    public function testGet(): void {
		[$db,$obj,$test] = $this->getObjects();
		$knr = "010/26";
		# Test::beg("MkKunde::get() Kundennummer: " . $knr);
		$test->add("testGet", "MkKunde::get() In: '". $knr . "'");

		$obj->get($knr);
		$dat = $obj->toString();
		MkKundeTest::$id = $obj->getId();
		[$id,$str,$msg,$success] = $this->result_output("testGet","MkKunde","Get");
		$test->fin("testGet", "MkKunde::get() Out: '" . $str . "', id=" . MkKundeTest::$id, $dat);

		$this->assertSame("OK 'Kunde 010/26 (" . MkKundeTest::$id. ")' gelesen!", $msg);
        $this->assertSame("Kunde 010/26 (" . MkKundeTest::$id . ")", $str);
		$this->assertTrue($success);
		# Test::end("MkKunde::get() Object: " . $str,$dat);
    }
    public function testUpdate(): void {
		[$db,$obj,$test] = $this->getObjects();
		$post = ['id'=>MkKundeTest::$id, 'gueltigkeit'=>'2027-08-31'];
		$test->add("testUpdate", "MkKunde::update() In: ". json_encode($post));
		$test->bet("testUpdate","MkKunde::update() Id: " . $obj->getId());

		$obj->set($post);
		$obj->update();
		$dat = $obj->toString();
		[$id,$str,$msg,$success] = $this->result_output("testUpdate","MkKunde","Update");
		$test->fin("testUpdate", "MkKunde::update() Out: '" . $str . "'", $dat);

		$this->assertSame("OK 'Kunde 010/26 (" . MkKundeTest::$id. ")' aktualisiert!", $msg);
        $this->assertStringEndsWith("(" . MkKundeTest::$id . ")", $str);
		$this->assertTrue($success);
    }
    public function testDelete(): void {
		[$db,$obj,$test] = $this->getObjects();
		$post = ['id' => MkKundeTest::$id ];
		$test->add("testDelete", "MkKunde::delete() In: ". json_encode($post));
		$obj->set($post);
		$obj->delete();
		$dat = $obj->toString();
		[$id,$str,$msg,$success] = $this->result_output("testDelete","MkKunde","Delete");
		$test->fin("testDelete", "MkKunde::delete() Out: " . $str, $dat);

		$this->assertSame("OK 'Kunde 010/26 (" . MkKundeTest::$id. ")' gelöscht!", $msg);
        $this->assertSame("Kunde NONE (0), Post NONE (".MkKundeTest::$id.")", $str);
		$this->assertTrue($success);
    }

	// ######################## TESTS Ende ############################

	// Testausgabe aller Tests, damit die Kurzdarstellung "...." oder "EE.." im ganzen erhalten bleibt.
	// Damit kann auch die Testausgabe insgesamt abgeschaltet werden.
	public function testOutput(): void {
		[$db,$obj,$test] = $this->getObjects();
		$test->outAll();
		$this->assertSame(1,1);
	}
}



