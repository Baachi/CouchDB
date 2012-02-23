<?php
namespace CouchDB\Tests\Http;

use CouchDB\Tests\TestCase;
use CouchDB\Http\SocketClient;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class SocketClientTest extends TestCase
{
    protected function setUp()
    {
        $this->markTestSkipped('Performance to low');
        $this->client = $this->getTestClient();
    }

    public function testConnect()
    {
        $this->assertFalse($this->client->isConnected());
        $this->client->connect();
        $this->assertTrue($this->client->isConnected());
    }

    public function testPutRequest()
    {
        $this->client->connect();
        $response = $this->client->request('/test', 'PUT');
        $this->assertTrue($response->isSuccessful());
    }

    public function testPostRequest()
    {
        $this->client->connect();
        $response = $this->client->request('/_all_dbs', 'GET');
        $this->assertInstanceOf('\\CouchDB\\Http\\Response\ResponseInterface', $response);
        $this->assertTrue($response->isSuccessful());
        $this->assertInternalType('string', $response->getContent());
    }

    protected function getTestClient()
    {
        static $c;
        if (null === $c) {
            $c = new SocketClient();
        }
        return $c;
    }
}
