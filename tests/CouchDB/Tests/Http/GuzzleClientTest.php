<?php
namespace CouchDB\Tests\Http;

use CouchDB\Http\GuzzleClient;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class GuzzleClientTest
{
    protected function setUp()
    {
        $this->client = new GuzzleClient();
    }

    public function testIsConnected()
    {
        $this->assertFalse($this->client->isConnected());
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
        $response = $this->client->request('/', BuzzClient::METHOD_POST, '{"test":"test"}', array());
        $this->assertInstanceOf('CouchDB\\Http\\Response\\Response', $response);
    }
    public function testDeleteRequest()
    {
        $response = $this->client->request('/', BuzzClient::METHOD_DELETE, '{"test":"test"}', array());
        $this->assertInstanceOf('CouchDB\\Http\\Response\\Response', $response);
    }
    public function testPutRequest()
    {
        $response = $this->client->request('/', BuzzClient::METHOD_PUT, '{"test":"test"}', array());
        $this->assertInstanceOf('CouchDB\\Http\\Response\\Response', $response);
    }
    public function testCopyRequest()
    {
        $response = $this->client->request('/', BuzzClient::METHOD_COPY, '{"test":"test"}', array());
        $this->assertInstanceOf('CouchDB\\Http\\Response\\Response', $response);
    }
}
