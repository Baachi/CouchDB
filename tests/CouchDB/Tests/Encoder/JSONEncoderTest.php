<?php

namespace CouchDB\Tests\Encoder;

use CouchDB\Encoder\JSONEncoder;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class JSONEncoderTest extends \PHPUnit_Framework_TestCase
{
    public function testEncode()
    {
        $this->assertEquals('{"foo":"bar"}', JSONEncoder::encode(['foo' => 'bar']));
    }

    public function testDecode()
    {
        $this->assertEquals(['foo' => 'bar'], JSONEncoder::decode('{"foo":"bar"}'));
    }
}
