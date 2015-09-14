<?php

namespace CouchDB\Tests\DesignDocument;

use CouchDB\DesignDocument\Result;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class ResultTest extends \PHPUnit_Framework_TestCase
{
    protected function setup()
    {
        $this->resultData = [
            'total_rows' => 51,
            'offset'     => 0,
            'rows'       => [
                [
                    'id'     => '64ACF01B05F53ACFEC48C062A5D01D89',
                    'key'    => null,
                    'value'  => [
                        'foo' => 'bar',
                    ],
                ],
                [
                    'id'    => '5D01D8964ACF01B05F53ACFEC48C062A',
                    'key'   => null,
                    'value' => [
                        'foo' => 'bar',
                    ],
                ],
                [
                    'id'    => 'EC48C062A5D01D8964ACF01B05F53ACF',
                    'key'   => null,
                    'value' => [
                        'foo' => 'bar',
                    ],
                ],
            ],
        ];

        $this->result = new Result($this->resultData);
    }

    public function testGetTotalRows()
    {
        $this->assertEquals(51, $this->result->getTotalRows());
    }

    public function testGetOffset()
    {
        $this->assertEquals(0, $this->result->getOffset());
    }

    public function testGetFirstRow()
    {
        $this->assertEquals($this->resultData['rows'][0], $this->result->getFirstRow());
    }

    public function testGetLastRow()
    {
        $this->assertEquals($this->resultData['rows'][2], $this->result->getLastRow());
    }

    public function testGetRows()
    {
        $this->assertEquals($this->resultData['rows'], $this->result->getRows());
    }

    public function testGetIterator()
    {
        $iterator = $this->result->getIterator();
        $this->assertInstanceOf('Iterator', $iterator);
        $this->assertEquals($this->resultData['rows'], $iterator->getArrayCopy());
    }

    public function testCount()
    {
        $this->assertCount(3, $this->result);
    }
}
