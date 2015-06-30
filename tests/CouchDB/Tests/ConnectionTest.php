<?php
namespace CouchDB\Tests;

use CouchDB\Connection;
use CouchDB\Exception\Exception;
use GuzzleHttp\Psr7\Response;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class ConnectionTest extends TestCase
{
    public function testListDatabases()
    {
        $this->mock->append(new Response(200, [], '["_users"]'));
        $this->assertEquals(['_users'], $this->connection->listDatabases());

        $request = $this->mock->getLastRequest();

        $this->assertEquals('GET', $request->getMethod());
        $this->assertEquals('/_all_dbs', $request->getUri()->getPath());
    }

    public function testCreateDatabase()
    {
        $this->mock->append(new Response(200, [], '{"ok": true}'));
        $this->connection->createDatabase('test');

        $request = $this->mock->getLastRequest();

        $this->assertEquals('PUT', $request->getMethod());
        $this->assertEquals('/test', $request->getUri()->getPath());
    }

    public function testSelectDatabase()
    {
        $this->mock->append(new Response(200, [], <<<JSON
{
    "compact_running": false,
    "db_name": "test",
    "disk_format_version": 5,
    "disk_size": 12377,
    "doc_count": 1,
    "doc_del_count": 1,
    "instance_start_time": "1267612389906234",
    "purge_seq": 0,
    "update_seq": 4
}
JSON
        ));

        $database = $this->connection->selectDatabase('test');
        $this->assertInstanceOf('CouchDB\\Database', $database);
        $this->assertEquals('test', $database->getName());

        $request = $this->mock->getLastRequest();

        $this->assertEquals('GET', $request->getMethod());
        $this->assertEquals('/test/', $request->getUri()->getPath());
    }

    public function testCreateExistingDatabase()
    {
        $this->mock->append(new Response(412, [], ''));

        try {
            $this->connection->createDatabase('test');
            $this->fail();
        } catch (Exception $e) {
            $this->assertEquals('The database "test" already exist', $e->getMessage());
        }
    }

    /**
     * @expectedException CouchDB\Exception\Exception
     */
    public function testFailingCreateDatabase()
    {
        $this->mock->append(new Response(200, [], '{"error":"conflict","reason":"Document update conflict."}'));

        $this->connection->createDatabase('test');
    }

    /**
     * @expectedException CouchDB\Exception\InvalidDatabasenameException
     */
    public function testCreateDatabaseWithInvalidName()
    {
        $this->connection->createDatabase('Test');
    }

    public function testGetNotExistingDatabase()
    {
        $this->mock->append(new Response(404, []));

        try {
            $this->connection->selectDatabase('test');
            $this->fail();
        } catch (Exception $e) {
            $this->assertEquals('The database "test" does not exist', $e->getMessage());
        }
    }

    public function testHasDatabase()
    {
        $this->mock->append(new Response(404, []));

        $this->assertFalse($this->connection->hasDatabase('test'));
    }

    public function testHasDatabaseWithExistingDatabase()
    {
        $this->mock->append(new Response(200, []));

        $this->assertTrue($this->connection->hasDatabase('test'));
    }

    public function testDropDatabase()
    {
        $this->mock->append(new Response(200, [], '{"ok": true}'));

        $this->connection->dropDatabase('test');

        $request = $this->mock->getLastRequest();

        $this->assertEquals('DELETE', $request->getMethod());
        $this->assertEquals('/test/', $request->getUri()->getPath());
    }

    public function testDropNotExistDatabase()
    {
        $this->mock->append(new Response(404, []));

        try {
            $this->connection->dropDatabase('test');
            $this->fail();
        } catch (Exception $e) {
            $this->assertEquals('The database "test" does not exist', $e->getMessage());
        }
    }

    public function testGetVersion()
    {
        $this->mock->append(new Response(200, [], '{"couchdb":"Welcome","version":"0.11.0"}'));

        $version = $this->connection->version();
        $request = $this->mock->getLastRequest();

        $this->assertEquals('0.11.0', $version);


        $this->assertEquals('GET', $request->getMethod());
        $this->assertEquals('/', $request->getUri()->getPath());
    }

    public function testGetEventManager()
    {
        $client = $this->getMock('GuzzleHttp\\ClientInterface');
        $evm = $this->getMock('Doctrine\\Common\\EventManager');

        $connection = new Connection($client, $evm);

        $this->assertEquals($evm, $connection->getEventManager());
    }
}
