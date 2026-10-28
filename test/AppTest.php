<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;
require_once __DIR__ . '/MkBaseTest.php';
require_once __DIR__ . '/Test.php';
require_once __DIR__ . '/../src/App.php';
require_once __DIR__ . '/../src/MkKunde.php';
require_once __DIR__ . '/../src/MkPerson.php';
require_once __DIR__ . '/../src/MkAdresse.php';
require_once __DIR__ . '/../src/MkKontakte.php';
require_once __DIR__ . '/../src/MkKontakt.php';
require_once __DIR__ . '/../src/MkTafel.php';
require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/Logger.php';

/*
*/

require_once __DIR__ . "/../ignore/config-test.php";

final class AppTest extends MkBaseTest {
	public static $id = 0;

	// Einmaliges Setup, wegen "static" müssen auch die Attribute "static" sein.
	public static function setUpBeforeClass(): void {				
		MkBaseTest::baseSetUpBeforeClass("AppTest");
		print "  - Setup MkTafel ...\n";
		MkBaseTest::$obj_static = new App();
		print "Test Setup Global finished!\n";
		print "===============================================\n";
		print "Test running:\n";
	}
	public static function tearDownAfterClass(): void {
		MkBaseTest::baseTearDownAfterClass();
		print "Test Tear Down Global finished!\n";
		print "===============================================\n";
	}

	// ######################## TESTS ############################

    public function testConstructor(): void {
		[$db,$obj,$test] = $this->getObjects();
		$test->add("testConstructor", "App() Create new Object.");
		$str = $obj->__toString();
        $this->assertSame("App Kunden DB=dbkunden", $str);
		$test->fin("testConstructor", "App() Object created: " . $str);
	}
    public function testGetKundenliste(): void {
		[$db,$obj,$test] = $this->getObjects();
		$test->add("testGetKundenliste", "App::getKundenliste() In: ''");

		$liste = $obj->getKundenliste();
		$n1 = $liste[0]['kundennummer'];
		$n2 = $liste[1]['kundennummer'];
		$n3 = $liste[2]['kundennummer'];
		$name1 = $liste[0]['nachname'];
		$pren1 = $liste[0]['vorname'];
		$test->fin("testGetKundenliste", "App::getKundenliste() Out: ", $liste);

		$this->assertSame("001/26", $n1);
		$this->assertSame("002/26", $n2);
		$this->assertSame("003/26", $n3);
		$this->assertSame("Nickels", $name1);
		$this->assertSame("Burkhard", $pren1);
    }
    public function testGetAction(): void {
		[$db,$obj,$test] = $this->getObjects();
		$test->add("testGetAction", "App::getAction() In: ''");
		$action = $obj->getAction();
		$action_type = $action[0];
		$action_subtype = $action[1];
		$test->fin("testGetAction", "App::getAction() Out: ", $action);
		$this->assertSame("", $action_type);
		$this->assertSame("", $action_subtype);
    }
    public function testGet(): void {
		[$db,$obj,$test] = $this->getObjects();
		$test->add("testGet", "App::get() In: ''");
		[$mk_kunde,$mk_person,$mk_adresse,$mk_kontakt] = $obj->get();
		$test->fin("testGet", "App::getAction() Out: ", $mk_kunde);
		$this->assertSame(0, $mk_kunde['id']);
		$this->assertSame(0, $mk_adresse['id']);
    }
    public function testDistribute(): void {
		[$db,$obj,$test] = $this->getObjects();
		$test->add("testDistribute", "App::distribute() In: ''");
		$array = $obj->distribute($post);
		$test->fin("testDistribute", "App::distribute() Out: ", $array);
		$this->assertSame(0, 0);
    }
    public function testSet(): void {
		// set($mk_kunde,$mk_person,$mk_adresse,$mk_tafel,$mk_kontakt)
		// setPost($post) // TBD
		// setPostVerify($post)
		// write()
		// delete()
		// getMsg()
		// printAll()
		// getKundendaten($nr = "")
		$this->assertSame(0, 0);
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

