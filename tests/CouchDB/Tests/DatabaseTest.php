<?php
namespace CouchDB\Tests;

use CouchDB\Database;
use CouchDB\Connection;
use GuzzleHttp\Psr7\Response;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class DatabaseTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->db = new Database('test', $this->connection, $this->client);
    }

    public function testGetConnection()
    {
        $this->assertEquals($this->connection, $this->db->getConnection());
    }

    public function testGetName()
    {
        $this->assertEquals('test', $this->db->getName());
    }

    public function testGetInfo()
    {
        $this->mock->append(new Response(200, [],
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
            }'
        ));

        $info = $this->db->getInfo();

        $request = $this->mock->getLastRequest();

        $this->assertEquals('GET', $request->getMethod());
        $this->assertEquals('/test/', $request->getUri()->getPath());

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
        $this->mock->append(new Response(201, [], '{"ok":true, "id":"123BAC", "rev":"946B7D1C"}'));
        $doc = array('author' => 'JohnDoe');

        $this->db->insert($doc);
        $request = $this->mock->getLastRequest();

        $this->assertEquals('POST', $request->getMethod());
        $this->assertEquals('/test/', $request->getUri()->getPath());
        $this->assertEquals('{"author":"JohnDoe"}', (string) $request->getBody());

        $this->assertEquals('123BAC', $doc['_id']);
        $this->assertEquals('946B7D1C', $doc['_rev']);
    }

    public function testInsertWithId()
    {
        $this->mock->append(new Response(201, [], '{"ok":true, "id":"john-doe", "rev":"946B7D1C"}'));

        $doc = array('author' => 'JohnDoe', '_id' => 'john-doe');
        $this->db->insert($doc);

        $request = $this->mock->getLastRequest();

        $this->assertEquals('PUT', $request->getMethod());
        $this->assertEquals('/test/john-doe', $request->getUri()->getPath());
        $this->assertEquals('{"author":"JohnDoe"}', (string) $request->getBody());

        $this->assertEquals('john-doe', $doc['_id']);
        $this->assertEquals('946B7D1C', $doc['_rev']);
    }

    /**
     * @expectedException CouchDB\Exception\Exception
     */
    public function testFailedInsert()
    {
        $this->mock->append(new Response(500, [], '{}'));

        $doc = array('author' => 'JohnDoe', '_id' => 'john-doe');
        $this->db->insert($doc);
    }

    public function testFind()
    {
        $this->mock->append(new Response(200, [],
            '{
                "_id":"john-doe",
                "_rev":"946B7D1C",
                "author": "johnDoe",
                "title": "CouchDB"
            }'
        ));

        $doc = $this->db->find('john-doe');

        $request = $this->mock->getLastRequest();

        $this->assertEquals('GET', $request->getMethod());
        $this->assertEquals('/test/john-doe', $request->getUri()->getPath());

        $this->assertEquals('john-doe', $doc['_id']);
        $this->assertEquals('946B7D1C', $doc['_rev']);
        $this->assertEquals('johnDoe', $doc['author']);
        $this->assertEquals('CouchDB', $doc['title']);
    }

    /**
     * @expectedException \CouchDB\Exception\Exception
     */
    public function testFindNotExistDocument()
    {
        $this->mock->append(new Response(404, [], '{}'));
        $this->db->find('john-doe');
    }

    public function testUpdate()
    {
        $this->mock->append(new Response(201, [], '{"ok":true, "id":"john-doe", "rev":"946B7D1C"}'));

        $doc = [
            '_id'    => 'john-doe',
            '_rev'   =>  '946B7D1C',
            'author' => 'johnDoe',
            'title'  => 'CouchDB',
        ];

        $this->db->update('john-doe', $doc);

        $this->assertEquals('john-doe', $doc['_id']);
        $this->assertEquals('946B7D1C', $doc['_rev']);
        $this->assertEquals('johnDoe', $doc['author']);
        $this->assertEquals('CouchDB', $doc['title']);

        $request = $this->mock->getLastRequest();

        $this->assertEquals('PUT', $request->getMethod());
        $this->assertEquals('/test/john-doe', $request->getUri()->getPath());
        $this->assertEquals(
            '{"_id":"john-doe","_rev":"946B7D1C","author":"johnDoe","title":"CouchDB"}',
            (string) $request->getBody()
        );
    }

    /**
     * @expectedException CouchDB\Exception\Exception
     */
    public function testFailUpdate()
    {
        $this->mock->append(new Response(500, [], '{}'));

        $doc = [
            '_id'    => 'john-doe',
            '_rev'   =>  '946B7D1C',
            'author' => 'johnDoe',
            'title'  => 'CouchDB',
        ];

        $this->db->update('john-doe', $doc);
    }

    public function testDelete()
    {
        $this->mock->append(new Response(200, [], '{"ok":true,"rev":"946B7D1C"}'));

        $this->assertTrue($this->db->delete('some-doc', '946B7D1C'));

        $request = $this->mock->getLastRequest();

        $this->assertEquals('DELETE', $request->getMethod());
        $this->assertEquals('/test/some-doc', $request->getUri()->getPath());
        $this->assertEquals('rev=946B7D1C', $request->getUri()->getQuery());
    }

    /**
     * @expectedException CouchDB\Exception\Exception
     */
    public function testFailingDelete()
    {
        $this->mock->append(new Response(500, [], '{}'));

        $this->assertTrue($this->db->delete('some-doc', '946B7D1C'));
    }

    public function testFindAll()
    {
        $this->mock->append(new Response(200, [], '{
  "total_rows": 3, "offset": 0, "rows": [
    {"id": "doc1", "key": "doc1", "value": {"rev": "4324BB"}},
    {"id": "doc2", "key": "doc2", "value": {"rev":"2441HF"}},
    {"id": "doc3", "key": "doc3", "value": {"rev":"74EC24"}}
  ]
}'));

        $result = $this->db->findAll(0, 'doc1');

        $this->assertEquals(3, $result['total_rows']);
        $this->assertEquals(0, $result['offset']);
        $this->assertCount(3, $result['rows']);

        $request = $this->mock->getLastRequest();

        $this->assertEquals('GET', $request->getMethod());
        $this->assertEquals('/test/_all_docs', $request->getUri()->getPath());
        $this->assertEquals('include_docs=true&limit=0&startkey=doc1', $request->getUri()->getQuery());
    }

    public function testFindDocuments()
    {
        $this->mock->append(new Response(200, [], '{
  "total_rows": 100, "offset": 0, "rows": [
    {"id": "doc1", "key": "1", "value": {"rev":"4324BB"}},
    {"id": "doc2", "key": "2", "value": {"rev":"2441HF"}}
  ]
}'));

        $docs = $this->db->findDocuments(array('1', '2'), 0, 100);
        $this->assertEquals(100, $docs['total_rows']);
        $this->assertCount(2, $docs['rows']);

        $request = $this->mock->getLastRequest();

        $this->assertEquals('POST', $request->getMethod());
        $this->assertEquals('/test/_all_docs', $request->getUri()->getPath());
        $this->assertEquals('include_docs=true&limit=0&skip=100', $request->getUri()->getQuery());
        $this->assertEquals('{"keys":["1","2"]}', (string) $request->getBody());
    }

    public function testCreateBatchUpdater()
    {
        $this->assertInstanceOf('CouchDB\\Util\\BatchUpdater', $this->db->createBatchUpdater());
    }

    public function testGetChanges()
    {
        $this->mock->append(new Response(200, [], '{"results":[
{"seq":1,"id":"fresh","changes":[{"rev":"1-967a00dff5e02add41819138abb3284d"}]},
{"seq":3,"id":"updated","changes":[{"rev":"2-7051cbe5c8faecd085a3fa619e6e6337"}]},
{"seq":5,"id":"deleted","changes":[{"rev":"2-eec205a9d413992850a6e32678485900"}],"deleted":true}
],
"last_seq":5}'));

        $changes = $this->db->getChanges();

        $this->assertArrayHasKey('results', $changes);
        $this->assertCount(3, $changes['results']);
        $this->assertEquals(5, $changes['last_seq']);

        $request = $this->mock->getLastRequest();

        $this->assertEquals('GET', $request->getMethod());
        $this->assertEquals('/test/_changes', $request->getUri()->getPath());
    }

    /**
     * @expectedException CouchDB\Exception\Exception
     */
    public function testFailChanges()
    {
        $this->mock->append(new Response(500, [], '{}'));
        $changes = $this->db->getChanges();
    }
}
