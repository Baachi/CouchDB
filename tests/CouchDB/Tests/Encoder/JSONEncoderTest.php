<?php
namespace CouchDB\Tests\Encoder;

use CouchDB\Tests\TestCase;
use CouchDB\Encoder\JSONEncoder;
use CouchDB\Exception\JsonDecodeException;
use CouchDB\Exception\JsonEncodeException;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class JSONEncoderTest extends TestCase
{
    /**
     * @dataProvider getEncodeData
     */
    public function testEncode($value, $expected)
    {
        $this->assertEquals($expected, JSONEncoder::encode($value));
    }

    /**
     * @dataProvider getDecodeData
     */
    public function testDecode($json, $expected)
    {
        $this->assertEquals($expected, JSONEncoder::decode($json));
    }

    public function testInvalidDecode()
    {
        try {
            JSONEncoder::decode('[invalid}');
            $this->fail();
        } catch (JsonDecodeException $e) {
            $this->assertEquals('Json decode error [Syntax error]: [invalid}', $e->getMessage());
        }
    }

    public function testInvalidEncode()
    {
        if (version_compare(PHP_VERSION, '5.3.2', '<=')) {
            $this->markTestSkipped('JSON_ERROR_UTF8 is only available in php version >5.3.2');
        }
        $this->setExpectedException('CouchDB\\Exception\\JsonEncodeException');
        JSONEncoder::encode("\xB1\x31");
    }

    static public function getEncodeData()
    {
        return array(
            array('foo', '"foo"'),
            array(1, '1'),
            array(1.1, '1.1'),
            array(array('foo', 'bar'), '["foo","bar"]'),
            array(array('foo' => 'bar'), '{"foo":"bar"}'),
        );
    }
    static public function getDecodeData()
    {
        return array(
            array('"foo"', 'foo'),
            array('1', 1),
            array('1.1', 1.1),
            array('["foo", "bar"]', array('foo', 'bar')),
            array('{"foo":"bar"}', array('foo' => 'bar')),
        );
    }

}
