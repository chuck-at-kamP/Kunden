<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

require_once __DIR__ . "/../ignore/config-test.php";

class MkBaseTest extends TestCase {
	public static $db_static;
	public static $obj_static;
	public static $test_static;

	public static $kunde_id;

	// Einmaliges Setup, wegen "static" müssen auch die Attribute "static" sein.
	protected static function baseSetUpBeforeClass($testclass): void {				
		print "===============================================\n";
		print "Test Setup Global ...\n";
		$name = DB_NAME;
		$user = DB_USER;
		$pass = DB_PASS;
		print "  - Setup Database ...\n";
		MkBaseTest::$db_static  = new Database($name, $user, $pass);
		print "  - Setup Test Statistik ...\n";
		MkBaseTest::$test_static = new Test($testclass);
	}
	protected static function baseTearDownAfterClass(): void {
		print "===============================================\n";
		print "Test Tear Down Global ...\n";
		Logger::info("=================== Tear Down ==================");
	}

	public function setUp(): void {  
		error_reporting(E_ALL);			// Muss vor jeden Test, sonst wird keine Warning angezeigt.
	}
	public function tearDown(): void {  }

	protected function getObjects() {		// Um eine Kurzschreibweise der Variablen zu haben.
		$db = MkBaseTest::$db_static;
		$obj = MkBaseTest::$obj_static;
		$test = MkBaseTest::$test_static;
		return [$db,$obj,$test];
	}

	protected function extraOutput() {
		$verboseString = getenv('VERBOSE');
		print "\n\nVerbose Mode: $verboseString\n";
		if($verboseString = "true") $verbose = true;
		else if($verboseString = "false") $verbose = false;
		else $verbose = true;

		if($verbose) {
			[$db,$obj,$test] = $this->getObjects();
			$test->outAll();
			$this->assertSame(1,1);
		}
	}
	protected function result_output($testName,$testClass,$text) {
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
	protected static function createKunde() {
		print "  - Setup MkKunde ...\n";
		$knr = '010/26';
		$post = ['kundennummer'=>$knr, 'tafel'=>'Tafel', 'kaufhaus'=>'Kaufhaus', 
			'vorname' => 'Hans', 'nachname' => 'Hansen',
			'nachweis'=>'ALG', 'gueltigkeit'=>'2026-12-31', 'bemerkung'=>'Unit Test'];
		$kunde = new MkKunde(MkBaseTest::$db_static);
		$kunde->set($post);
		$kunde->write();
		if($kunde->success()) {
			MkBaseTest::$kunde_id = $kunde->getId();
			print "    " . $kunde->getMsg() . "\n";
		} else {
			print "    Status: " . $kunde->getMsg() . "\n";
			print "    Error : " . $kunde->getErr() . "\n";
			$kunde->get($knr);
			MkBaseTest::$kunde_id = $kunde->getId();
			print "    " . $kunde->getMsg() . "\n";
		}
		print "  - Setup Test MkKunde " . MkBaseTest::$kunde_id . "\n";
	}
	protected static function deleteKunde() {
		$kunde = new MkKunde(MkBaseTest::$db_static);
		$post = ['id' => MkBaseTest::$kunde_id ];
		$kunde->set($post);
		$kunde->delete();
		if($kunde->success()) {
			print "   Kunde " . $kunde . " gelöscht!\n";
		}
		else {
			print "   Kunde " . $kunde . " nicht gelöscht!\n";
			print "   Error: " . $kunde->getMsg() . ", error=" . $kunde->getErr() . "\n";
		}
	}
}

