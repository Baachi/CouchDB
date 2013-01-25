<?php
namespace CouchDB\Tests\Http;

use CouchDB\Tests\TestCase;
use CouchDB\Http\SocketClient;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class SocketClientTest extends TestCase
{
    public function testIsNotConnectedInitially()
    {
        $client = new SocketClient();
        $this->assertFalse($client->isConnected());
    }

    public function testConnect()
    {
        $client = new SocketClient();
        $client->connect();
        $this->assertTrue($client->isConnected());
    }

    public function testRequest()
    {
        $client = new SocketClient();
        $client->connect();

        $response = $client->request('/');

        $this->assertTrue($response->isSuccessful());
    }

    public function testCanUseSameConnectionForMultipleRequests()
    {
        $client = new SocketClient();
        $client->connect();

        $client->request('/', 'GET');
        $client->request('/', 'GET');
        $client->request('/', 'GET');
    }
}
