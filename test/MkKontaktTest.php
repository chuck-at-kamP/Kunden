<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;
require_once __DIR__ . '/MkBaseTest.php';
require_once __DIR__ . '/Test.php';
require_once __DIR__ . '/../src/MkKontakt.php';
require_once __DIR__ . '/../src/MkKunde.php';
require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/Logger.php';

/*
*/

final class MkKontaktTest extends MkBaseTest {
	public static $id = 0;

	// Einmaliges Setup, wegen "static" müssen auch die Attribute "static" sein.
	public static function setUpBeforeClass(): void {				
		MkBaseTest::baseSetUpBeforeClass("MkKontaktTest");
		MkBaseTest::createKunde();
		print "  - Setup MkKontakt ...\n";
		MkBaseTest::$obj_static = new MkKontakt(MkBaseTest::$db_static);
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
		$test->add("testConstructor", "MkKontakt() Create new Object.");			// Erzeugung einer Testausgabe am Anfang des Tests
		$str = $obj->__toString();
        $this->assertSame("Kontakt None None (0/0)", $str);
		$test->fin("testConstructor", "MkKontakt() Object created: " . $str);		// Erzeugung einer Testausgabe am Ende des Tests
	}

    public function testConstructor1(): void {
		[$db,$obj,$test] = $this->getObjects();
		$test->add("testConstructor1", "MkKontakt() Create new Object.");
		$obj1 = new MkKontakt(MkKontaktTest::$db_static);
        $this->assertSame("Kontakt None None (0/0)", $obj1->__toString());
		$str = $obj1->__toString();
		$test->fin("testConstructor1", "MkKontakt() Object created: " . $str);
    }

    public function testWrite(): void {
		[$db,$obj,$test] = $this->getObjects();
		$post = ['kunde_id'=>MkBaseTest::$kunde_id, 'k_type'=>'Festnetz', 'kontakt'=>'040444444'];
		$test->add("testWrite", "MkKontakt::write() In: ", $post);
		$obj->set($post);
		$ret = $obj->write();
		$test->bet("testWrite","MkKontakt::write() Returns: ", $ret);
		[$id,$str,$msg,$success] = $this->result_output("testWrite","MkKontakt","Create");
		$test->fin("testWrite", "MkKontakt::write() Out: " . $str . ", msg=" . $msg, $ret);

		MkKontaktTest::$id = $id;
		$kid = MkBaseTest::$kunde_id;
		$this->assertSame('[{"id":' . $id . ',"kunde_id":' . $kid . ',"kontakt":"040444444"}]', json_encode($ret));
        $this->assertSame("Kontakt Festnetz 040444444 ($id/" . MkBaseTest::$kunde_id . ")", $str);
		$this->assertSame("OK 'Kontakt Festnetz 040444444 (" . $id . "/" . MkBaseTest::$kunde_id . ")' eingerichtet!", $msg);
		$this->assertTrue($success);
    }
    public function testGet(): void {
		[$db,$obj,$test] = $this->getObjects();
		$test->add("testGet", "MkKontakt::get() In: '". MkKontaktTest::$id . "'");

		$obj->get(MkKontaktTest::$id);
		$dat = $obj->toString();
		[$id,$str,$msg,$success] = $this->result_output("testGet","MkKontakt","Get");
		$test->fin("testGet", "MkKontakt::get() Out: '" . $str . "', id=" . MkKontaktTest::$id, $dat);

		$this->assertSame("OK 'Kontakt Festnetz 040444444 (" . MkKontaktTest::$id. "/" . MkBaseTest::$kunde_id . ")' gelesen!", $msg);
        $this->assertSame("Kontakt Festnetz 040444444 (" . $id . "/" . MkBaseTest::$kunde_id . ")", $str);
		$this->assertTrue($success);
    }
    public function testUpdate(): void {
		[$db,$obj,$test] = $this->getObjects();
		$post = ['id'=>MkKontaktTest::$id, 'kontakt'=>'040555555'];
		$test->add("testUpdate", "MkKontakt::update() In: ". json_encode($post));
		$test->bet("testUpdate","MkKontakt::update() Id: " . $obj->getId());

		$obj->set($post);
		$obj->update();
		$dat = $obj->toString();
		[$id,$str,$msg,$success] = $this->result_output("testUpdate","MkKontakt","Update");
		$test->fin("testUpdate", "MkKontakt::update() Out: '" . $str . "'", $dat);

		$this->assertSame("OK 'Kontakt Festnetz 040555555 (" . MkKontaktTest::$id. "/" . MkBaseTest::$kunde_id . ")' aktualisiert!", $msg);
        $this->assertStringEndsWith("(" . MkKontaktTest::$id . "/" . MkBaseTest::$kunde_id . ")", $str);
		$this->assertTrue($success);
    }
    public function testDelete(): void {
		[$db,$obj,$test] = $this->getObjects();
		$post = ['id' => MkKontaktTest::$id ];
		$test->add("testDelete", "MkKontakt::delete() In: ". json_encode($post));
		$obj->set($post);
		$obj->delete();
		$dat = $obj->toString();
		[$id,$str,$msg,$success] = $this->result_output("testDelete","MkKontakt","Delete");
		$test->fin("testDelete", "MkKontakt::delete() Out: " . $str, $dat);

		$this->assertSame("OK 'Kontakt Festnetz 040555555 (" . MkKontaktTest::$id. "/" . MkBaseTest::$kunde_id . ")' gelöscht!", $msg);
        $this->assertSame("Kontakt None None (0/0)", $str);
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

