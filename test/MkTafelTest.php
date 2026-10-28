<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;
require_once __DIR__ . '/MkBaseTest.php';
require_once __DIR__ . '/Test.php';
require_once __DIR__ . '/../src/MkTafel.php';
require_once __DIR__ . '/../src/MkKunde.php';
require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/Logger.php';

/*
*/

final class MkTafelTest extends MkBaseTest {
	public static $id = 0;

	// Einmaliges Setup, wegen "static" müssen auch die Attribute "static" sein.
	public static function setUpBeforeClass(): void {				
		MkBaseTest::baseSetUpBeforeClass("MkTafelTest");
		MkBaseTest::createKunde();
		print "  - Setup MkTafel ...\n";
		MkBaseTest::$obj_static = new MkTafel(MkBaseTest::$db_static);
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
		$test->add("testConstructor", "MkTafel() Create new Object.");			// Erzeugung einer Testausgabe am Anfang des Tests
		$str = $obj->__toString();
        $this->assertSame("Tafel None 0/0 (0/0)", $str);
		$test->fin("testConstructor", "MkTafel() Object created: " . $str);		// Erzeugung einer Testausgabe am Ende des Tests
	}

    public function testConstructor1(): void {
		[$db,$obj,$test] = $this->getObjects();
		$test->add("testConstructor1", "MkTafel() Create new Object.");
		$obj1 = new MkTafel(MkTafelTest::$db_static);
        $this->assertSame("Tafel None 0/0 (0/0)", $obj1->__toString());
		$str = $obj1->__toString();
		$test->fin("testConstructor1", "MkTafel() Object created: " . $str);
    }

    public function testWrite(): void {
		[$db,$obj,$test] = $this->getObjects();
		$post = ['kunde_id'=>MkBaseTest::$kunde_id, 'personen'=>1, 'preis'=>2, 'gruppe'=>'blau'];
		$test->add("testWrite", "MkTafel::write() In: ", $post);
		$obj->set($post);
		$ret = $obj->write();
		$test->bet("testWrite","MkTafel::write() Returns: ", $ret);
		[$id,$str,$msg,$success] = $this->result_output("testWrite","MkTafel","Create");
		$test->fin("testWrite", "MkTafel::write() Out: " . $str . ", msg=" . $msg, $ret);

		MkTafelTest::$id = $id;
		$kid = MkBaseTest::$kunde_id;
		$this->assertSame('[{"id":' . $id . ',"kunde_id":' . $kid . ',"gruppe":"blau"}]', json_encode($ret));
        $this->assertSame("Tafel blau 1/2 ($id/" . MkBaseTest::$kunde_id . ")", $str);
		$this->assertSame("OK 'Tafel blau 1/2 (" . $id . "/" . MkBaseTest::$kunde_id . ")' eingerichtet!", $msg);
		$this->assertTrue($success);
    }
    public function testGet(): void {
		[$db,$obj,$test] = $this->getObjects();
		$test->add("testGet", "MkTafel::get() In: '". MkTafelTest::$id . "'");

		$obj->get(MkTafelTest::$id);
		$dat = $obj->toString();
		[$id,$str,$msg,$success] = $this->result_output("testGet","MkTafel","Get");
		$test->fin("testGet", "MkTafel::get() Out: '" . $str . "', id=" . MkTafelTest::$id, $dat);

		$this->assertSame("OK 'Tafel blau 1/2 (" . MkTafelTest::$id. "/" . MkBaseTest::$kunde_id . ")' gelesen!", $msg);
        $this->assertSame("Tafel blau 1/2 (" . $id . "/" . MkBaseTest::$kunde_id . ")", $str);
		$this->assertTrue($success);
    }
    public function testUpdate(): void {
		[$db,$obj,$test] = $this->getObjects();
		$post = ['id'=>MkTafelTest::$id, 'personen'=>2];
		$test->add("testUpdate", "MkTafel::update() In: ". json_encode($post));
		$test->bet("testUpdate","MkTafel::update() Id: " . $obj->getId());

		$obj->set($post);
		$obj->update();
		$dat = $obj->toString();
		[$id,$str,$msg,$success] = $this->result_output("testUpdate","MkTafel","Update");
		$test->fin("testUpdate", "MkTafel::update() Out: '" . $str . "'", $dat);

		$this->assertSame("OK 'Tafel blau 2/2 (" . MkTafelTest::$id. "/" . MkBaseTest::$kunde_id . ")' aktualisiert!", $msg);
        $this->assertStringEndsWith("(" . MkTafelTest::$id . "/" . MkBaseTest::$kunde_id . ")", $str);
		$this->assertTrue($success);
    }
    public function testDelete(): void {
		[$db,$obj,$test] = $this->getObjects();
		$post = ['id' => MkTafelTest::$id ];
		$test->add("testDelete", "MkTafel::delete() In: ". json_encode($post));
		$obj->set($post);
		$obj->delete();
		$dat = $obj->toString();
		[$id,$str,$msg,$success] = $this->result_output("testDelete","MkTafel","Delete");
		$test->fin("testDelete", "MkTafel::delete() Out: " . $str, $dat);

		$this->assertSame("OK 'Tafel blau 2/2 (" . MkTafelTest::$id. "/" . MkBaseTest::$kunde_id . ")' gelöscht!", $msg);
        $this->assertSame("Tafel None 0/0 (0/0)", $str);
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



