<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;
require_once __DIR__ . '/MkBaseTest.php';
require_once __DIR__ . '/Test.php';
require_once __DIR__ . '/../src/MkKunde.php';
require_once __DIR__ . '/../src/MkPerson.php';
require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/Logger.php';


final class MkPersonTest extends MkBaseTest {
	public static $id = 0;

	// Einmaliges Setup, wegen "static" müssen auch die Attribute "static" sein.
	public static function setUpBeforeClass(): void {				
		MkBaseTest::baseSetUpBeforeClass("MkPersonTest");
		MkBaseTest::createKunde();
		print "  - Setup MkPerson ...\n";
		MkBaseTest::$obj_static = new MkPerson(MkBaseTest::$db_static);
		print "Test Setup Global finished!\n";
		print "===============================================\n";
		print "Test running:\n";
	}
	public static function tearDownAfterClass(): void {
		MkBaseTest::baseTearDownAfterClass();
		MkBaseTest::deleteKunde();
		print "Test Tear Down Global finished!\n";
		print "===============================================\n";
	}

	// ######################## TESTS ############################

    public function testConstructor(): void {
		[$db,$obj,$test] = $this->getObjects();
		$test->add("testConstructor", "MkPerson() Create new Object.");			// Erzeugung einer Testausgabe am Anfang des Tests
		$str = $obj->__toString();
        $this->assertSame("Person None None (0/0)", $str);
		$test->fin("testConstructor", "MkPerson() Object created: " . $str);		// Erzeugung einer Testausgabe am Ende des Tests
	}

    public function testConstructor1(): void {
		[$db,$obj,$test] = $this->getObjects();
		$test->add("testConstructor1", "MkPerson() Create new Object.");
		$obj1 = new MkPerson(MkPersonTest::$db_static,"Kunde");
        $this->assertSame("Person None None (0/0)", $obj1->__toString());
		$str = $obj1->__toString();
		$test->fin("testConstructor1", "MkPerson() Object created: " . $str);
    }

    public function testWrite(): void {
		[$db,$obj,$test] = $this->getObjects();
		$post = ['kunde_id'=>MkBaseTest::$kunde_id, 'vorname'=>'Jens', 'nachname'=>'Jensen-Helfer'];
		$test->add("testWrite", "MkPerson::write() In: ", $post);
		$obj->set($post);
		$ret = $obj->write();
		$test->bet("testWrite","MkPerson::write() Returns: ", $ret);
		[$id,$str,$msg,$success] = $this->result_output("testWrite","MkPerson","Create");
		$test->fin("testWrite", "MkPerson::write() Out: " . $str . ", msg=" . $msg, $ret);

		MkPersonTest::$id = $id;
		$kid = MkBaseTest::$kunde_id;
		$this->assertSame('[{"id":' . $id . ',"kunde_id":' . $kid . ',"vorname":"Jens","nachname":"Jensen-Helfer"}]', json_encode($ret));
        $this->assertSame("Person Jens Jensen-Helfer (" . $id . "/" . $kid . ")", $str);
		$this->assertSame("OK 'Person Jens Jensen-Helfer (" . $id . "/" . $kid . ")' eingerichtet!", $msg);
		$this->assertTrue($success);
    }
    public function testGet(): void {
		[$db,$obj,$test] = $this->getObjects();
		$test->add("testGet", "MkPerson::get() In: '". MkPersonTest::$id . "'");

		$obj->get(MkPersonTest::$id);
		$dat = $obj->toString();
		[$id,$str,$msg,$success] = $this->result_output("testGet","MkPerson","Get");
		$test->fin("testGet", "MkPerson::get() Out: '" . $str . "', id=" . MkPersonTest::$id, $dat);

		$this->assertSame("OK 'Person Jens Jensen-Helfer (" . MkPersonTest::$id. "/" . MkBaseTest::$kunde_id . ")' gelesen!", $msg);
        $this->assertSame("Person Jens Jensen-Helfer (" . $id . "/" . MkBaseTest::$kunde_id . ")", $str);
		$this->assertTrue($success);
    }
    public function testUpdate(): void {
		[$db,$obj,$test] = $this->getObjects();
		$post = ['id'=>MkPersonTest::$id, 'vorname'=>'Jens-Neu'];
		$test->add("testUpdate", "MkPerson::update() In: ". json_encode($post));
		$test->bet("testUpdate","MkPerson::update() Id: " . $obj->getId());

		$obj->set($post);
		$obj->update();
		$dat = $obj->toString();
		[$id,$str,$msg,$success] = $this->result_output("testUpdate","MkPerson","Update");
		$test->fin("testUpdate", "MkPerson::update() Out: '" . $str . "'", $dat);

		$this->assertSame("OK 'Person Jens-Neu Jensen-Helfer (" . MkPersonTest::$id. "/" . MkBaseTest::$kunde_id . ")' aktualisiert!", $msg);
        $this->assertStringEndsWith("(" . MkPersonTest::$id . "/" . MkBaseTest::$kunde_id . ")", $str);
		$this->assertTrue($success);
    }
    public function testDelete(): void {
		[$db,$obj,$test] = $this->getObjects();
		$post = ['id' => MkPersonTest::$id ];
		$test->add("testDelete", "MkPerson::delete() In: ". json_encode($post));
		$obj->set($post);
		$obj->delete();
		$dat = $obj->toString();
		[$id,$str,$msg,$success] = $this->result_output("testDelete","MkPerson","Delete");
		$test->fin("testDelete", "MkPerson::delete() Out: " . $str, $dat);

		$this->assertSame("OK 'Person Jens-Neu Jensen-Helfer (" . MkPersonTest::$id. "/" . MkBaseTest::$kunde_id . ")' gelöscht!", $msg);
        $this->assertSame("Person None None (0/0)", $str);
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

