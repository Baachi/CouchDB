<?php
namespace CouchDB\Tests\Encoder;

use CouchDB\Tests\TestCase;
use CouchDB\Encoder\NativeEncoder;
use CouchDB\Exception\JsonDecodeException;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class NativeEncoderTest extends TestCase
{
    protected function setUp()
    {
        $this->encoder = new NativeEncoder();
    }

    /**
     * @dataProvider getEncodeData
     */
    public function testEncode($value, $expected)
    {
        $this->assertEquals($expected, $this->encoder->encode($value));
    }

    /**
     * @dataProvider getDecodeData
     */
    public function testDecode($json, $expected)
    {
        $this->assertEquals($expected, $this->encoder->decode($json));
    }

    public function testInvalidDecode()
    {
        try {
            $this->encoder->decode('[invalid}');
            $this->fail('Syntax error');
        } catch (JsonDecodeException $e) {
            $this->assertEquals('Json decode error [Syntax error]: [invalid}', $e->getMessage());
        }
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
