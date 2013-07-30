<?php
namespace CouchDB\Tests\Http;

use CouchDB\Tests\TestCase;
use CouchDB\Http\SocketClient;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class SocketClientTest extends TestCase
{
    /**
     * @group http
     */
    public function testIsNotConnectedInitially()
    {
        $client = new SocketClient();
        $this->assertFalse($client->isConnected());
    }

    /**
     * @group http
     */
    public function testConnect()
    {
        $client = new SocketClient();
        $client->connect();
        $this->assertTrue($client->isConnected());
    }

    /**
     * @group http
     */
    public function testRequest()
    {
        $client = new SocketClient();
        $client->connect();

        $response = $client->request('/');

        $this->assertTrue($response->isSuccessful());
    }

    /**
     * @group http
     */
    public function testCanUseSameConnectionForMultipleRequests()
    {
        $client = new SocketClient();
        $client->connect();

        $client->request('/', 'GET');
        $client->request('/', 'GET');
        $client->request('/', 'GET');
    }
}
