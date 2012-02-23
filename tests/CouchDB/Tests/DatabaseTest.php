<?php
namespace CouchDB\Tests;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class DatabaseTest extends TestCase
{
    protected function setUp()
    {
        $this->db = $this->createTestDatabase('test');
    }

    protected function tearDown()
    {
        $conn = $this->createTestConnection();
        if (isset($conn->test)) {
            unset($conn->test);
        }
    }

    public function testGetName()
    {
        $this->assertEquals('test', $this->db->getName());
    }

    public function testGetConnection()
    {
        $conn = $this->db->getConnection();
        $this->assertInstanceOf('CouchDB\\Connection', $conn);
    }

    public function testGetInfo()
    {
        $info = $this->db->getInfo();
        $this->assertInternalType('array', $info);
        $this->assertArrayHasKey('db_name', $info);
        $this->assertEquals('test', $info['db_name']);

        $this->assertArrayHasKey('compact_running', $info);
        $this->assertArrayHasKey('disk_format_version', $info);
        $this->assertArrayHasKey('disk_size', $info);
        $this->assertArrayHasKey('doc_count', $info);
        $this->assertArrayHasKey('doc_del_count', $info);
        $this->assertArrayHasKey('instance_start_time', $info);
        $this->assertArrayHasKey('purge_seq', $info);
        $this->assertArrayHasKey('update_seq', $info);
    }

    public function testInsertWithOutId()
    {
        $doc = array('author' => 'JohnDoe');
        $this->db->insert($doc);
        $this->assertArrayHasKey('_id', $doc);
        $this->assertArrayHasKey('_rev', $doc);
    }

    public function testInsertWithId()
    {
        $doc = array('author' => 'John Doe', '_id' => 'john-doe');
        $this->db->insert($doc);
        $this->assertArrayHasKey('_id', $doc);
        $this->assertEquals('john-doe', $doc['_id']);
    }

    public function testFind()
    {
        $doc = array('author' => 'John Doe');
        $this->db->insert($doc);

        $doc2 = $this->db->find($doc['_id']);
        $this->assertEquals($doc, $doc2);
    }

    /**
     * @expectedException RuntimeException
     */
    public function testFindNotExistDocument()
    {
        $this->db->find('123');
    }

    public function testUpdate()
    {
        $doc = array('foo' => 'bar');
        $this->db->insert($doc);
        $doc['foo'] = 'foobar';
        $this->db->update($doc['_id'], $doc);

        $doc2 = $this->db->find($doc['_id']);
        $this->assertEquals($doc['foo'], $doc2['foo']);
        $this->assertEquals($doc['_id'], $doc2['_id']);
    }

    public function testDelete()
    {
        $doc = array('foo' => 'bar');
        $this->db->insert($doc);

        $this->assertTrue($this->db->delete($doc['_id'], $doc['_rev']));

        try {
            $doc = $this->db->find($doc['_id']);
            var_dump($doc);
            $this->fail('Find should be fail');
        } catch (\RuntimeException $e) {
        }
    }

    public function testFindAll()
    {
        $docs = $this->db->findAll();
        $this->assertEquals(0, $docs['total_rows']);
        $this->assertEquals(array(), $docs['rows']);

        $doc = array('foo' => 'bar');
        $this->db->insert($doc);

        $docs = $this->db->findAll();
        $this->assertEquals(1, $docs['total_rows']);
        $this->assertEquals($doc['_id'], $docs['rows'][0]['id']);
    }

    public function testFindDocuments()
    {
        for ($i = 0; $i < 100; $i++) {
            $data = array('foo' => 'bar', '_id' => trim($i));
            $this->db->insert($data);
        }

        $docs = $this->db->findDocuments(array('1', '2'));
        $this->assertEquals(100, $docs['total_rows']);
        $this->assertCount(2, $docs['rows']);

        $docs = $this->db->findDocuments(array('1', '2', '3', '4', '5', '6', '7', '8', '9', '10'), 10);
        $this->assertEquals(100, $docs['total_rows']);
        $this->assertCount(10, $docs['rows']);

        $docs = $this->db->findDocuments(array('1', '2', '3', '4', '5'), 5, 2);
        $this->assertEquals(100, $docs['total_rows']);
        $this->assertCount(3, $docs['rows']);
    }

    public function testCreateBatchUpdater()
    {
        $batchUpdater = $this->db->createBatchUpdater();
        $this->assertInstanceOf('CouchDB\\Util\\BatchUpdater', $batchUpdater);
    }

    public function testGetChanges()
    {
        $changes = $this->db->getChanges();
        $this->assertInternalType('array', $changes);
        $this->assertArrayHasKey('results', $changes);
        $this->assertArrayHasKey('last_seq', $changes);
    }
}
