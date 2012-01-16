<?php
namespace CouchDB\Tests;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class DatabaseTest extends TestCase
{
    protected function setUp()
    {
        $this->database = $this->createTestDatabase();
    }

    protected function tearDown()
    {
        $this->createTestConnection()->dropDatabase($this->database->getName());
    }

    public function testGetName()
    {
        $this->assertEquals('test', $this->database->getName());
    }

    public function testFindAll()
    {
        $docs = $this->database->findAll();
        $this->assertInternalType('array', $docs);
        $this->assertEquals(array('total_rows' => 0, 'offset' => 0, 'rows' => array()), $docs);
    }

    public function testInsert()
    {
        $doc = array(
            'book' => 'CouchDB Manual',
            'author' => 'John Doe',
        );

        list($id) = $this->database->insert($doc);
        $this->assertInternalType('string', $id);
        $this->assertEquals($doc, $this->database->find($id));
    }
}
