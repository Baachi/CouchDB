<?php
namespace CouchDB\Tests\Http\Response;

use CouchDB\Http\Response\Response;
use CouchDB\Tests\TestCase;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class ResponseTest extends TestCase
{
    protected function setUp()
    {
        $this->response = new Response(200, '{"status":"404"}', array('Content-Type' => 'application/json'));
    }

    public function testGetContent()
    {
        $this->assertEquals('{"status":"404"}', $this->response->getContent());
    }

    public function testGetStatusCode()
    {
        $this->assertEquals(200, $this->response->getStatusCode());
    }

    public function testGetHeader()
    {
        $this->assertEquals('application/json', $this->response->getHeader('Content-Type'));
        $this->assertNull($this->response->getHeader('not exist'));
    }

    public function testGetHeaders()
    {
        $this->assertEquals(array('Content-Type' => 'application/json'), $this->response->getHeaders());
    }

    public function testToString()
    {
        $this->assertEquals('{"status":"404"}', (string) $this->response);
    }

    public function testIsSuccessful()
    {
        $this->assertTrue($this->response->isSuccessful());
        $response = new Response(500, '', array());
        $this->assertFalse($response->isSuccessful());
    }

    /**
     * @dataProvider successfulHttpCodesProvider
     */
    public function testSuccessfulCodes($code)
    {
        $response = new Response($code, '', array());
        $this->assertTrue($response->isSuccessful());
    }

    public static function successfulHttpCodesProvider()
    {
        return array(
            array(200),
            array(201),
            array(202),
            array(203),
            array(204),
            array(205),
            array(206),
        );
    }
}
