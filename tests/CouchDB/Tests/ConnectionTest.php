<?php
namespace CouchDB\Tests;

use CouchDB\Connection;
use CouchDB\Http\Response\Response;
use CouchDB\Http;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class ConnectionTest extends TestCase
{
    public function testCreate()
    {
        $connection = Connection::create();

        $this->assertInstanceOf('CouchDB\\Http\\SocketClient', $connection->getClient());

        $connection = Connection::create(array(
            'client'         => 'stream',
            'username'       => 'john',
            'password'       => 'password',
        ));

        $this->assertInstanceOf('CouchDB\\Http\\StreamClient', $connection->getClient());
        $this->assertEquals('john', $connection->getClient()->getOption('username'));
        $this->assertEquals('password', $connection->getClient()->getOption('password'));
    }

    public function testIsConnected()
    {
        $client = $this->getMock('CouchDB\\Http\\ClientInterface');
        $client->expects($this->exactly(2))
            ->method('isConnected')
            ->will($this->returnValue(false));

        $client->expects($this->once())
            ->method('connect');

        $connection = new Connection($client);

        $this->assertFalse($connection->isConnected());
        $connection->initialize();
    }

    public function testListDatabases()
    {
        $response = new Response(200, '["_users"]', array());

        $client = $this->getMock('CouchDB\\Http\\ClientInterface');
        $client->expects($this->once())
            ->method('request')
            ->with('/_all_dbs')
            ->will($this->returnValue($response));

        $conn = new Connection($client);

        $databases = $conn->listDatabases();
        $this->assertEquals(array('_users'), $databases);
    }

    public function testCreateDatabase()
    {
        $client = $this->getMock('CouchDB\\Http\\ClientInterface');

        $response = new Response(201, '{"ok": true}', array());

        $client->expects($this->once())
            ->method('request')
            ->with('/test/', 'PUT')
            ->will($this->returnValue($response));

        $connection = new Connection($client);

        $connection->createDatabase('test');
    }

    public function testSelectDatabase()
    {
        $client = $this->getMock('CouchDB\\Http\\ClientInterface');

        $response = new Response(200,
            '{
                "compact_running": false,
                "db_name": "test",
                "disk_format_version": 5,
                "disk_size": 12377,
                "doc_count": 1,
                "doc_del_count": 1,
                "instance_start_time": "1267612389906234",
                "purge_seq": 0,
                "update_seq": 4
            }',
            array()
        );

        $client->expects($this->once())
            ->method('request')
            ->with('/test/', 'GET')
            ->will($this->returnValue($response));

        $connection = new Connection($client);

        $database = $connection->selectDatabase('test');
        $this->assertInstanceOf('CouchDB\\Database', $database);
        $this->assertEquals('test', $database->getName());
    }

    public function testCreateExistingDatabase()
    {
        $client = $this->getMock('CouchDB\\Http\\ClientInterface');

        $response = new Response(412, '', array());

        $client->expects($this->once())
            ->method('request')
            ->with('/test/', 'PUT')
            ->will($this->returnValue($response));

        $connection = new Connection($client);

        try {
            $connection->createDatabase('test');
            $this->fail();
        } catch (\RuntimeException $e) {
            $this->assertEquals('The database test already exist', $e->getMessage());
        }
    }

    public function testCreateDatabaseWithInvalidName()
    {
        $client = $this->getMock('CouchDB\\Http\\ClientInterface');
        $client->expects($this->never())
            ->method('connect');

        $connection = new Connection($client);

        try {
            $connection->createDatabase('Test');
            $this->fail();
        } catch (\RuntimeException $e) {
            $this->assertEquals('The database name Test is invalid. The database name must match the following pattern (a-z0-9_$()+-)', $e->getMessage());
        }
    }

    public function testGetNotExistingDatabase()
    {
        $client = $this->getMock('CouchDB\\Http\\ClientInterface');
        $response = new Response(404, '', array());

        $client->expects($this->once())
            ->method('request')
            ->with('/test/', 'GET')
            ->will($this->returnValue($response));

        $connection = new Connection($client);

        try {
            $connection->selectDatabase('test');
            $this->fail();
        } catch (\RuntimeException $e) {
            $this->assertEquals('The database test does not exist', $e->getMessage());
        }
    }

    public function testHasDatabase()
    {
        $client = $this->getMock('CouchDB\\Http\\ClientInterface');
        $response = new Response(404, '', array());

        $client->expects($this->once())
            ->method('request')
            ->with('/test/', 'GET')
            ->will($this->returnValue($response));

        $connection = new Connection($client);

        $this->assertFalse($connection->hasDatabase('test'));
    }

    public function testHasDatabaseWithExistingDatabase()
    {
        $client = $this->getMock('CouchDB\\Http\\ClientInterface');
        $response = new Response(200, '', array());

        $client->expects($this->once())
            ->method('request')
            ->with('/test/', 'GET')
            ->will($this->returnValue($response));

        $connection = new Connection($client);

        $this->assertTrue($connection->hasDatabase('test'));
    }

    public function testDropDatabase()
    {
        $client = $this->getMock('CouchDB\\Http\\ClientInterface');
        $response = new Response(200, '{"ok": true}', array());

        $client->expects($this->once())
            ->method('request')
            ->with('/test/', 'DELETE')
            ->will($this->returnValue($response));

        $connection = new Connection($client);

        $connection->dropDatabase('test');
    }

    public function testDropNotExistDatabase()
    {
        $client = $this->getMock('CouchDB\\Http\\ClientInterface');
        $response = new Response(404, '', array());

        $client->expects($this->once())
            ->method('request')
            ->with('/test/', 'DELETE')
            ->will($this->returnValue($response));

        $connection = new Connection($client);

        try {
            $connection->dropDatabase('test');
            $this->fail();
        } catch (\RuntimeException $e) {
            $this->assertEquals('The database test does not exist', $e->getMessage());
        }
    }

    public function testGetVersion()
    {
        $client = $this->getMock('CouchDB\\Http\\ClientInterface');
        $response = new Response(200, '{"couchdb":"Welcome","version":"0.11.0"}', array());

        $client->expects($this->once())
            ->method('request')
            ->with('/', 'GET')
            ->will($this->returnValue($response));

        $connection = new Connection($client);

        $version = $connection->version();

        $this->assertEquals('0.11.0', $version);
    }

    public function testGetAndSetClient()
    {
        $client = $this->getMock('CouchDB\\Http\\ClientInterface');
        $connection = new Connection($client);

        $this->assertEquals($client, $connection->getClient());

        $client2 = $this->getMock('CouchDB\\Http\\ClientInterface');
        $connection->setClient($client2);

        $this->assertEquals($client2, $connection->getClient());
    }

    public function testGetEventManager()
    {
        $client = $this->getMock('CouchDB\\Http\\ClientInterface');
        $evm = $this->getMock('Doctrine\\Common\\EventManager');

        $connection = new Connection($client, $evm);

        $this->assertEquals($evm, $connection->getEventManager());
    }
}