<?php
namespace CouchDB\Tests;

use CouchDB\Database;
use CouchDB\Connection;
use CouchDB\Http\Response\Response;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class DatabaseTest extends TestCase
{
    public function testGetName()
    {
        $connection = $this->getMockBuilder('CouchDB\\Connection')
            ->disableOriginalConstructor()
            ->getMock();

        $db = new Database('test', $connection);

        $this->assertEquals('test', $db->getName());
    }

    public function testGetConnection()
    {
        $connection = $this->getMockBuilder('CouchDB\\Connection')
            ->disableOriginalConstructor()
            ->getMock();

        $db = new Database('test', $connection);

        $this->assertEquals($connection, $db->getConnection());
    }

    public function testGetInfo()
    {
        $client = $this->getMock('CouchDB\\Http\\ClientInterface');
        $connection = new Connection($client);

        $db = new Database('test', $connection);

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

        $info = $db->getInfo();

        $this->assertEquals('test', $info['db_name']);
        $this->assertEquals(false, $info['compact_running']);
        $this->assertEquals(5, $info['disk_format_version']);
        $this->assertEquals(12377, $info['disk_size']);
        $this->assertEquals(1, $info['doc_count']);
        $this->assertEquals(1, $info['doc_del_count']);
        $this->assertEquals("1267612389906234", $info['instance_start_time']);
        $this->assertEquals(0, $info['purge_seq']);
        $this->assertEquals(4, $info['update_seq']);
    }

    public function testInsertWithOutId()
    {
        $client = $this->getMock('CouchDB\\Http\\ClientInterface');
        $connection = new Connection($client);

        $response = new Response(201, '{"ok":true, "id":"123BAC", "rev":"946B7D1C"}', array());

        $client->expects($this->once())
            ->method('request')
            ->with('/test/', 'POST', '{"author":"JohnDoe"}')
            ->will($this->returnValue($response));

        $db = new Database('test', $connection);
        $doc = array('author' => 'JohnDoe');
        $db->insert($doc);

        $this->assertEquals('123BAC', $doc['_id']);
        $this->assertEquals('946B7D1C', $doc['_rev']);
    }

    public function testInsertWithId()
    {
        $client = $this->getMock('CouchDB\\Http\\ClientInterface');
        $connection = new Connection($client);

        $response = new Response(201, '{"ok":true, "id":"john-doe", "rev":"946B7D1C"}', array());

        $client->expects($this->once())
            ->method('request')
            ->with('/test/john-doe', 'PUT', '{"author":"JohnDoe"}')
            ->will($this->returnValue($response));

        $db = new Database('test', $connection);
        $doc = array('author' => 'JohnDoe', '_id' => 'john-doe');
        $db->insert($doc);

        $this->assertEquals('john-doe', $doc['_id']);
        $this->assertEquals('946B7D1C', $doc['_rev']);
    }

    public function testFind()
    {
        $client = $this->getMock('CouchDB\\Http\\ClientInterface');
        $connection = new Connection($client);

        $response = new Response(
            200,
            '{
                "_id":"john-doe",
                "_rev":"946B7D1C",
                "author": "johnDoe",
                "title": "CouchDB"
            }',
            array()
        );

        $client->expects($this->once())
            ->method('request')
            ->with('/test/john-doe', 'GET')
            ->will($this->returnValue($response));

        $db = new Database('test', $connection);

        $doc = $db->find('john-doe');

        $this->assertEquals('john-doe', $doc['_id']);
        $this->assertEquals('946B7D1C', $doc['_rev']);
        $this->assertEquals('johnDoe', $doc['author']);
        $this->assertEquals('CouchDB', $doc['title']);
    }

    /**
     * @expectedException RuntimeException
     */
    public function testFindNotExistDocument()
    {
        $client = $this->getMock('CouchDB\\Http\\ClientInterface');
        $connection = new Connection($client);

        $response = new Response(404, '{}', array());

        $client->expects($this->once())
            ->method('request')
            ->with('/test/john-doe', 'GET')
            ->will($this->returnValue($response));

        $db = new Database('test', $connection);

        $db->find('john-doe');
    }

    public function testUpdate()
    {
        $client = $this->getMock('CouchDB\\Http\\ClientInterface');
        $connection = new Connection($client);

        $response = new Response(
            201,
            '{"ok":true, "id":"john-doe", "rev":"946B7D1C"}',
            array()
        );

        $client->expects($this->once())
            ->method('request')
            ->with('/test/john-doe', 'PUT', '{"_id":"john-doe","_rev":"946B7D1C","author":"johnDoe","title":"CouchDB"}')
            ->will($this->returnValue($response));

        $db = new Database('test', $connection);

        $doc = array(
            '_id'    => 'john-doe',
            '_rev'   =>  '946B7D1C',
            'author' => 'johnDoe',
            'title'  => 'CouchDB',
        );

        $db->update('john-doe', $doc);

        $this->assertEquals('john-doe', $doc['_id']);
        $this->assertEquals('946B7D1C', $doc['_rev']);
        $this->assertEquals('johnDoe', $doc['author']);
        $this->assertEquals('CouchDB', $doc['title']);
    }

    public function testDelete()
    {
        $client = $this->getMock('CouchDB\\Http\\ClientInterface');
        $connection = new Connection($client);

        $response = new Response(200, '{"ok":true,"rev":"946B7D1C"}', array());

        $client->expects($this->once())
            ->method('request')
            ->with('/test/some-doc?rev=946B7D1C', 'DELETE')
            ->will($this->returnValue($response));

        $db = new Database('test', $connection);

        $this->assertTrue($db->delete('some-doc', '946B7D1C'));
    }

    public function testFindAll()
    {
        $client = $this->getMock('CouchDB\\Http\\ClientInterface');
        $connection = new Connection($client);

        $response = new Response(200, '{
  "total_rows": 3, "offset": 0, "rows": [
    {"id": "doc1", "key": "doc1", "value": {"rev": "4324BB"}},
    {"id": "doc2", "key": "doc2", "value": {"rev":"2441HF"}},
    {"id": "doc3", "key": "doc3", "value": {"rev":"74EC24"}}
  ]
}', array());

        $client->expects($this->once())
            ->method('request')
            ->with('/test/_all_docs?include_docs=true', 'GET')
            ->will($this->returnValue($response));

        $db = new Database('test', $connection);
        $result = $db->findAll();

        $this->assertEquals(3, $result['total_rows']);
        $this->assertEquals(0, $result['offset']);
        $this->assertCount(3, $result['rows']);
    }

    public function testFindDocuments()
    {
        $client = $this->getMock('CouchDB\\Http\\ClientInterface');
        $connection = new Connection($client);

        $response = new Response(200, '{
  "total_rows": 100, "offset": 0, "rows": [
    {"id": "doc1", "key": "1", "value": {"rev":"4324BB"}},
    {"id": "doc2", "key": "2", "value": {"rev":"2441HF"}}
  ]
}', array());

        $client->expects($this->once())
            ->method('request')
            ->with('/test/_all_docs?include_docs=true', 'POST', '{"keys":["1","2"]}')
            ->will($this->returnValue($response));

        $db = new Database('test', $connection);

        $docs = $db->findDocuments(array('1', '2'));
        $this->assertEquals(100, $docs['total_rows']);
        $this->assertCount(2, $docs['rows']);
    }

    public function testCreateBatchUpdater()
    {
        $client = $this->getMock('CouchDB\\Http\\ClientInterface');
        $connection = new Connection($client);
        $db = new Database('test', $connection);

        $this->assertInstanceOf('CouchDB\\Util\\BatchUpdater', $db->createBatchUpdater());
    }

    public function testGetChanges()
    {
        $client = $this->getMock('CouchDB\\Http\\ClientInterface');
        $connection = new Connection($client);

        $response = new Response(200, '{"results":[
{"seq":1,"id":"fresh","changes":[{"rev":"1-967a00dff5e02add41819138abb3284d"}]},
{"seq":3,"id":"updated","changes":[{"rev":"2-7051cbe5c8faecd085a3fa619e6e6337"}]},
{"seq":5,"id":"deleted","changes":[{"rev":"2-eec205a9d413992850a6e32678485900"}],"deleted":true}
],
"last_seq":5}', array());

        $client->expects($this->once())
            ->method('request')
            ->with('/test/_changes', 'GET')
            ->will($this->returnValue($response));

        $db = new Database('test', $connection);

        $changes = $db->getChanges();

        $this->assertArrayHasKey('results', $changes);
        $this->assertCount(3, $changes['results']);
        $this->assertEquals(5, $changes['last_seq']);
    }
}
