<?php
namespace CouchDB\Tests;

use CouchDB\Cursor;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class CursorTest extends TestCase
{
    protected function setUp()
    {
        $this->couchData = array(
            0 => array(
                '_id' => '1',
                'author' => 'John Wayne',
                'title'  => 'Wayness',
            ),
            1 => array(
                '_id' => '2',
                'author' => 'John Wayne',
                'title'  => 'Wayne',
            )
        );
        $this->cursor = new Cursor($this->couchData);
    }

    public function testIterator()
    {
        $iterator = $this->cursor;
        $this->assertInstanceOf('Iterator', $iterator);

        $this->assertEquals(current($this->couchData), $this->cursor->current());
        $this->assertEquals(0, $this->cursor->key());
        $this->assertTrue($this->cursor->valid());

        $this->cursor->next();
        next($this->couchData);
        $this->assertEquals(current($this->couchData), $this->cursor->current());
        $this->assertEquals(1, $this->cursor->key());

        $this->cursor->next();
        $this->assertFalse($this->cursor->valid());

        $this->cursor->rewind();
        reset($this->couchData);
        $this->assertEquals(current($this->couchData), $this->cursor->current());
        $this->assertTrue($this->cursor->valid());

        $this->cursor->next();
        $this->assertEquals(current($this->couchData), $this->cursor->previous());
    }

    public function testToArray()
    {
        $this->assertEquals($this->couchData, $this->cursor->toArray());
    }

    public function testCountable()
    {
        $this->assertEquals(2, $this->cursor->count());
        $this->assertEquals(2, count($this->cursor));
    }

    public function testFilterMethods()
    {
        $filtered = $this->cursor->filter(function ($document) {
            return 'Wayne' === $document['title'];
        });
        $this->assertNotEquals($filtered, $this->cursor);
        $this->assertInstanceOf('CouchDB\\Cursor', $filtered);
        $this->assertEquals(1, count($filtered));
    }

    public function testMap()
    {
        $mapped = $this->cursor->map(function ($document) {
            $document['title'] = strtoupper($document['title']);
            return $document;
        });

        $this->assertNotEquals($mapped, $this->cursor);
        $this->assertEquals(2, count($mapped));

        $document = $mapped->current();
        $this->assertEquals('WAYNESS', $document['title']);
        $this->assertEquals('John Wayne', $document['author']);
    }

    public function testSort()
    {
        $sorted = $this->cursor->sort(function ($a, $b) {
            return strcmp($a['_id'], $a['_id']);
        });

        $this->assertNotEquals(
            spl_object_hash($sorted),
            spl_object_hash($this->cursor)
        );
        $this->assertEquals(count($sorted), count($this->cursor));
    }

    public function testFields()
    {
        $this->assertEquals(array('_id', 'author', 'title'), $this->cursor->fields());
    }

    public function testFirstAndLast()
    {
        $this->assertEquals(reset($this->couchData), $this->cursor->first());
        $this->assertEquals(end($this->couchData), $this->cursor->last());
    }
}
