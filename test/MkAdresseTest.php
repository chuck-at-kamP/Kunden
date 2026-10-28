<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;
require_once __DIR__ . '/MkBaseTest.php';
require_once __DIR__ . '/Test.php';
require_once __DIR__ . '/../src/MkAdresse.php';
require_once __DIR__ . '/../src/MkKunde.php';
require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/Logger.php';

/*
*/

final class MkAdresseTest extends MkBaseTest {
	public static $id = 0;

	// Einmaliges Setup, wegen "static" müssen auch die Attribute "static" sein.
	public static function setUpBeforeClass(): void {				
		MkBaseTest::baseSetUpBeforeClass("MkAdresseTest");
		MkBaseTest::createKunde();
		print "  - Setup MkAdresse ...\n";
		MkBaseTest::$obj_static = new MkAdresse(MkBaseTest::$db_static);
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
		$test->add("testConstructor", "MkAdresse() Create new Object.");			// Erzeugung einer Testausgabe am Anfang des Tests
		$str = $obj->__toString();
        $this->assertSame("Adresse None, None None (0/0)", $str);
		$test->fin("testConstructor", "MkAdresse() Object created: " . $str);		// Erzeugung einer Testausgabe am Ende des Tests
	}

    public function testConstructor1(): void {
		[$db,$obj,$test] = $this->getObjects();
		$test->add("testConstructor1", "MkAdresse() Create new Object.");
		$obj1 = new MkAdresse(MkAdresseTest::$db_static);
        $this->assertSame("Adresse None, None None (0/0)", $obj1->__toString());
		$str = $obj1->__toString();
		$test->fin("testConstructor1", "MkAdresse() Object created: " . $str);
    }

    public function testWrite(): void {
		[$db,$obj,$test] = $this->getObjects();
		$post = ['kunde_id'=>MkBaseTest::$kunde_id, 'strasse'=>'Dorfstr', 'hausnummer'=>'10', 'plz'=>'22022', 'ort'=>'Hansdorf'];
		$test->add("testWrite", "MkAdresse::write() In: ", $post);
		$obj->set($post);
		$ret = $obj->write();
		$test->bet("testWrite","MkAdresse::write() Returns: ", $ret);
		[$id,$str,$msg,$success] = $this->result_output("testWrite","MkAdresse","Create");
		$test->fin("testWrite", "MkAdresse::write() Out: " . $str . ", msg=" . $msg, $ret);

		MkAdresseTest::$id = $id;
		$kid = MkBaseTest::$kunde_id;
		$this->assertSame('[{"id":' . $id . ',"kunde_id":' . $kid . ',"strasse":"Dorfstr","hausnummer":"10"}]', json_encode($ret));
        $this->assertSame("Adresse 22022, Dorfstr 10 ($id/" . MkBaseTest::$kunde_id . ")", $str);
		$this->assertSame("OK 'Adresse 22022, Dorfstr 10 (" . $id . "/" . MkBaseTest::$kunde_id . ")' eingerichtet!", $msg);
		$this->assertTrue($success);
    }
    public function testGet(): void {
		[$db,$obj,$test] = $this->getObjects();
		$test->add("testGet", "MkAdresse::get() In: '". MkAdresseTest::$id . "'");

		$obj->get(MkAdresseTest::$id);
		$dat = $obj->toString();
		[$id,$str,$msg,$success] = $this->result_output("testGet","MkAdresse","Get");
		$test->fin("testGet", "MkAdresse::get() Out: '" . $str . "', id=" . MkAdresseTest::$id, $dat);

		$this->assertSame("OK 'Adresse 22022, Dorfstr 10 (" . MkAdresseTest::$id. "/" . MkBaseTest::$kunde_id . ")' gelesen!", $msg);
        $this->assertSame("Adresse 22022, Dorfstr 10 (" . $id . "/" . MkBaseTest::$kunde_id . ")", $str);
		$this->assertTrue($success);
    }
    public function testUpdate(): void {
		[$db,$obj,$test] = $this->getObjects();
		$post = ['id'=>MkAdresseTest::$id, 'hausnummer'=>'10a'];
		$test->add("testUpdate", "MkAdresse::update() In: ". json_encode($post));
		$test->bet("testUpdate","MkAdresse::update() Id: " . $obj->getId());

		$obj->set($post);
		$obj->update();
		$dat = $obj->toString();
		[$id,$str,$msg,$success] = $this->result_output("testUpdate","MkAdresse","Update");
		$test->fin("testUpdate", "MkAdresse::update() Out: '" . $str . "'", $dat);

		$this->assertSame("OK 'Adresse 22022, Dorfstr 10a (" . MkAdresseTest::$id. "/" . MkBaseTest::$kunde_id . ")' aktualisiert!", $msg);
        $this->assertStringEndsWith("(" . MkAdresseTest::$id . "/" . MkBaseTest::$kunde_id . ")", $str);
		$this->assertTrue($success);
    }
    public function testDelete(): void {
		[$db,$obj,$test] = $this->getObjects();
		$post = ['id' => MkAdresseTest::$id ];
		$test->add("testDelete", "MkAdresse::delete() In: ". json_encode($post));
		$obj->set($post);
		$obj->delete();
		$dat = $obj->toString();
		[$id,$str,$msg,$success] = $this->result_output("testDelete","MkAdresse","Delete");
		$test->fin("testDelete", "MkAdresse::delete() Out: " . $str, $dat);

		$this->assertSame("OK 'Adresse 22022, Dorfstr 10a (" . MkAdresseTest::$id. "/" . MkBaseTest::$kunde_id . ")' gelöscht!", $msg);
        $this->assertSame("Adresse None, None None (0/0)", $str);
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



