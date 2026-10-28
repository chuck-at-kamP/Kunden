<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;
require_once __DIR__ . '/MkBaseTest.php';
require_once __DIR__ . '/Test.php';
require_once __DIR__ . '/../src/MkKontakte.php';
require_once __DIR__ . '/../src/MkKunde.php';
require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/Logger.php';

/*
*/

final class MkKontakteTest extends MkBaseTest {
	public static $id = 0;

	// Einmaliges Setup, wegen "static" müssen auch die Attribute "static" sein.
	public static function setUpBeforeClass(): void {				
		MkBaseTest::baseSetUpBeforeClass("MkKontakteTest");
		MkBaseTest::createKunde();
		print "  - Setup MkKontakte ...\n";
		MkBaseTest::$obj_static = new MkKontakte(MkBaseTest::$db_static);
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
		$test->add("testConstructor", "MkKontakte() Create new Object.");			// Erzeugung einer Testausgabe am Anfang des Tests
		$str = $obj->__toString();
		$test->fin("testConstructor", "MkKontakte() Object created: " . $str);		// Erzeugung einer Testausgabe am Ende des Tests
        $this->assertSame("Kontakte (0): ", $str);
	}

    public function testConstructor1(): void {
		[$db,$obj,$test] = $this->getObjects();
		$test->add("testConstructor1", "MkKontakte() Create new Object.");
		$obj1 = new MkKontakte($db);
		$str = $obj1->__toString();
		$test->fin("testConstructor1", "MkKontakte() Object created: " . $str);
        $this->assertSame("Kontakte (0): ", $obj1->__toString());
    }

	/*
    public function testWrite(): void {
		[$db,$obj,$test] = $this->getObjects();
		$post = ['kunde_id'=>MkBaseTest::$kunde_id, 'k_type'=>'Festnetz', 'kontakt'=>'040444444'];
		$test->add("testWrite", "MkKontakte::write() In: ", $post);
		$obj->set($post);
		$ret = $obj->write();
		$test->bet("testWrite","MkKontakte::write() Returns: ", $ret);
		[$id,$str,$msg,$success] = $this->result_output("testWrite","MkKontakte","Create");
		$test->fin("testWrite", "MkKontakte::write() Out: " . $str . ", msg=" . $msg, $ret);

		MkKontakteTest::$id = $id;
		$kid = MkBaseTest::$kunde_id;
		$this->assertSame('[{"id":' . $id . ',"kunde_id":' . $kid . ',"kontakt":"040444444"}]', json_encode($ret));
        $this->assertSame("Kontakte Festnetz 040444444 ($id/" . MkBaseTest::$kunde_id . ")", $str);
		$this->assertSame("OK 'Kontakte Festnetz 040444444 (" . $id . "/" . MkBaseTest::$kunde_id . ")' eingerichtet!", $msg);
		$this->assertTrue($success);
    }
	*/
    public function testGet(): void {
		[$db,$obj,$test] = $this->getObjects();
		$test->add("testGet", "MkKontakte::get() In: '". MkKontakteTest::$id . "'");

		$obj->get(MkKontakteTest::$id);
		$dat = $obj->toString();
		[$id,$str,$msg,$success] = $this->result_output("testGet","MkKontakte","Get");
		$test->fin("testGet", "MkKontakte::get() Out: '" . $str . "', id=" . MkKontakteTest::$id, $dat);

		$this->assertSame("OK 'Kontakte Festnetz 040444444 (" . MkKontakteTest::$id. "/" . MkBaseTest::$kunde_id . ")' gelesen!", $msg);
        $this->assertSame("Kontakte Festnetz 040444444 (" . $id . "/" . MkBaseTest::$kunde_id . ")", $str);
		$this->assertTrue($success);
    }
	/*
    public function testUpdate(): void {
		[$db,$obj,$test] = $this->getObjects();
		$post = ['id'=>MkKontakteTest::$id, 'kontakt'=>'040555555'];
		$test->add("testUpdate", "MkKontakte::update() In: ". json_encode($post));
		$test->bet("testUpdate","MkKontakte::update() Id: " . $obj->getId());

		$obj->set($post);
		$obj->update();
		$dat = $obj->toString();
		[$id,$str,$msg,$success] = $this->result_output("testUpdate","MkKontakte","Update");
		$test->fin("testUpdate", "MkKontakte::update() Out: '" . $str . "'", $dat);

		$this->assertSame("OK 'Kontakte Festnetz 040555555 (" . MkKontakteTest::$id. "/" . MkBaseTest::$kunde_id . ")' aktualisiert!", $msg);
        $this->assertStringEndsWith("(" . MkKontakteTest::$id . "/" . MkBaseTest::$kunde_id . ")", $str);
		$this->assertTrue($success);
    }
	*/
	/*
    public function testDelete(): void {
		[$db,$obj,$test] = $this->getObjects();
		$post = ['id' => MkKontakteTest::$id ];
		$test->add("testDelete", "MkKontakte::delete() In: ". json_encode($post));
		$obj->set($post);
		$obj->delete();
		$dat = $obj->toString();
		[$id,$str,$msg,$success] = $this->result_output("testDelete","MkKontakte","Delete");
		$test->fin("testDelete", "MkKontakte::delete() Out: " . $str, $dat);

		$this->assertSame("OK 'Kontakte Festnetz 040555555 (" . MkKontakteTest::$id. "/" . MkBaseTest::$kunde_id . ")' gelöscht!", $msg);
        $this->assertSame("Kontakte None None (0/0)", $str);
		$this->assertTrue($success);
    }
	*/

	// ######################## TESTS Ende ############################

	// Testausgabe aller Tests, damit die Kurzdarstellung "...." oder "EE.." im ganzen erhalten bleibt.
	// Damit kann auch die Testausgabe insgesamt abgeschaltet werden.
	public function testOutput(): void { $this->extraOutput(); }
}

