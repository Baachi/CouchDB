<?php
namespace CouchDB\Tests;

use CouchDB\Connection;
use CouchDB\Http;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class ConnectionTest extends TestCase
{
    protected function setUp()
    {
        $this->conn = $this->createTestConnection();
    }

    protected function tearDown()
    {
        if (isset($this->conn->test)) {
            unset($this->conn->test);
        }
    }

    public function testIsConnected()
    {
        $this->assertFalse($this->conn->isConnected());
        $this->conn->initialize();
        $this->assertTrue($this->conn->isConnected());
    }

    public function testListDatabases()
    {
        $databases = $this->conn->listDatabases();
        $this->assertContains('_users', $databases);
    }

    public function testGetDatabase()
    {
        $this->conn->createDatabase('test');
        $database = $this->conn->selectDatabase('test');
        $this->assertInstanceOf('\\CouchDB\\Database', $database);
        $this->assertEquals('test', $database->getName());
    }

    public function testCreateDatabase()
    {
        $database = $this->conn->createDatabase('test');
        $this->assertInstanceOf('\\CouchDB\\Database', $database);
        $this->assertEquals('test', $database->getName());
    }

    public function testCreateExistingDatabase()
    {
        $this->conn->createDatabase('test');
        try {
            $this->conn->createDatabase('test');
            $this->fail();
        } catch (\RuntimeException $e) {
            $this->assertEquals('The database test already exist', $e->getMessage());
        }
    }

    public function testCreateDatabaseWithInvalidName()
    {
        try {
            $this->conn->createDatabase('Test');
            $this->fail();
        } catch (\RuntimeException $e) {
            $this->assertEquals('The database name Test is invalid. The database name must match the following pattern (a-z0-9_$()+-', $e->getMessage());
        }
    }

    public function testGetNotExistingDatabase()
    {
        try {
            $this->conn->selectDatabase('test');
            $this->fail();
        } catch (\RuntimeException $e) {
            $this->assertEquals('The database test does not exist', $e->getMessage());
        }
    }

    public function testHasDatabase()
    {
        $this->assertFalse($this->conn->hasDatabase('test'));
        $this->conn->createDatabase('test');
        $this->assertTrue($this->conn->hasDatabase('test'));
    }

    public function testDropDatabase()
    {
        $this->conn->createDatabase('test');
        $this->assertTrue($this->conn->hasDatabase('test'));
        $this->conn->dropDatabase('test');
        $this->assertFalse($this->conn->hasDatabase('test'));
    }

    public function testDropNotExistDatabase()
    {
        try {
            $this->conn->dropDatabase('test');
            $this->fail();
        } catch (\RuntimeException $e) {
            $this->assertEquals('The database test does not exist', $e->getMessage());
        }
    }

    public function testGetVersion()
    {
        $version = $this->conn->version();
        $this->assertInternalType('string', $version);
    }

    public function testGetAndSetClient()
    {
        $mock = $this->getMock('CouchDB\\Http\\ClientInterface');
        $this->assertInstanceOf('CouchDB\\Http\\ClientInterface', $client = $this->conn->getClient());
        $this->assertNull($this->conn->setClient($mock));
        $this->conn->setClient($client);
    }

    public function testGetEventManager()
    {
        $this->assertInstanceOf('Doctrine\\Common\\EventManager', $this->conn->getEventManager());
    }

    public function testMagicFunctions()
    {
        $this->conn->createDatabase('test');
        $db = $this->conn->test;
        $this->assertInstanceOf('CouchDB\\Database', $db);
        $this->assertEquals('test', $db->getName());

        $this->assertTrue(isset($this->conn->test));
        unset($this->conn->test);
        $this->assertFalse(isset($this->conn->test));
    }

    public function testUtilizesAuthAdapter()
    {
        $authAdapter = $this->getMock('CouchDB\Auth\AuthInterface');
        $authAdapter->expects($this->once())->method('authorize');

        $conn = new Connection(new Http\StreamClient(), null, $authAdapter);
        $conn->initialize();
    }
}
