<?php
namespace CouchDB\Tests\Http;

use CouchDB\Tests\TestCase;
use CouchDB\Http\BuzzClient;
use CouchDB\Http\Response\Response;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class BuzzClientTest extends TestCase
{
    protected function setUp()
    {
        $browser = $this->getMock('Buzz\\Browser');
        $browser->expects($this->any())
            ->method('call')
            ->will($this->returnValue(new Response(200, 'test', array())));
        $this->client = new BuzzClient($browser);
    }

    public function testIsConnected()
    {
        $this->assertTrue($this->client->isConnected());
        $this->client->connect();
        $this->assertTrue($this->client->isConnected());
    }

    public function testGetRequest()
    {
        $response = $this->client->request('/');
        $this->assertInstanceOf('CouchDB\\Http\\Response\\Response', $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($response->isSuccessful());
    }

    public function testPostRequest()
    {
        $response = $this->client->request('/', BuzzClient::METHOD_POST, array(), '{"test":"test"}');
        $this->assertInstanceOf('CouchDB\\Http\\Response\\Response', $response);
    }
    public function testDeleteRequest()
    {
        $response = $this->client->request('/', BuzzClient::METHOD_DELETE, array(), '{"test":"test"}');
        $this->assertInstanceOf('CouchDB\\Http\\Response\\Response', $response);
    }
    public function testPutRequest()
    {
        $response = $this->client->request('/', BuzzClient::METHOD_PUT, array(), '{"test":"test"}');
        $this->assertInstanceOf('CouchDB\\Http\\Response\\Response', $response);
    }
    public function testCopyRequest()
    {
        $response = $this->client->request('/', BuzzClient::METHOD_COPY, array(), '{"test":"test"}');
        $this->assertInstanceOf('CouchDB\\Http\\Response\\Response', $response);
    }
}