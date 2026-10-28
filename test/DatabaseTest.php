<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;
require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/Logger.php';

require_once __DIR__ . "/../ignore/config-test.php";

class DatabaseTest extends TestCase
{
	protected $db;

	public function setUp(): void {
		error_reporting(E_ALL);
		$name = DB_NAME;
		$user = DB_USER;
		$pass = DB_PASS;
		$this->db = new Database($name, $user, $pass);
	}

    public function testConstructor(): void {
        $this->assertSame("DB dbkunden, login=kunden", $this->db->__toString());
	}

	public function testConvertDate(): void {
		$date1 = $this->db->convertDate("31.12.2026");
        $this->assertSame("2026-12-31", $date1);
    }

    public function testConstructorFails(): void
    {
        $this->expectException(PDOException::class);

		$name = "dbkunden2";
		$user = DB_USER;
		$pass = DB_PASS;
		$db = new Database($name, $user, $pass);
    }
}
