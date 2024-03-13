<?php

namespace Tests\Unit;

use stdClass;
use PDOStatement;
use Dotenv\Dotenv;
use App\Core\config;
use App\Core\Database;
use PHPUnit\Framework\TestCase;

class DatabaseTest extends TestCase
{
    private Database $database;

    protected function setUp(): void
    {

        $this->database = Database::getInstance();
    }

    public function testQuery()
    {
        $sql = 'SELECT * FROM users WHERE id = 1';
        $result = $this->database->query($sql);
        $this->assertInstanceOf(Database::class, $result);
    }

    public function testFind()
    {
        $sql = 'SELECT * FROM users WHERE id = 1';
        $this->database->query($sql);
        $user = $this->database->find();
        $this->assertInstanceOf(stdClass::class, $user);
    }

    public function testFindAll()
    {
        $sql = 'SELECT * FROM users';
        $this->database->query($sql);
        $users = $this->database->findAll();
        $this->assertInstanceOf(stdClass::class,($users));
    }

    public function testFindOrFail()
    {
        $sql = 'SELECT * FROM users WHERE id = 1';
        $this->database->query($sql);
        $user = $this->database->findOrFail();
        $this->assertInstanceOf(stdClass::class, $user);

        $sql = 'SELECT * FROM users WHERE id = 3';
        $this->database->query($sql);
        $user = $this->database->findOrFail();
        $this->assertNull($user);
    }

    public function testRowCount()
    {
        $sql = 'SELECT * FROM users';
        $this->database->query($sql);
        $rowCount = $this->database->rowCount();
        $this->assertEquals(2, $rowCount);
    }

    public function testLastInsertId()
    {
        $sql = 'INSERT INTO users (name, email) VALUES (?, ?)';
        $this->database->query($sql, ['John Doe', 'john.doe@example.com']);
        $lastInsertId = $this->database->lastInsertId();
        $this->assertEquals(3, $lastInsertId);
    }

    public function testConfig()
    {
        $config = config('database');

        $this->assertIsArray($config);
        $this->assertArrayHasKey('host', $config);
        $this->assertArrayHasKey('database', $config);
        $this->assertArrayHasKey('username', $config);
        $this->assertArrayHasKey('password', $config);
    }
}
