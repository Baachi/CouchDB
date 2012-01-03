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
        $this->client = new SocketClient();
    }

    public function testConnect()
    {
        $this->assertFalse($this->client->isConnected());
        $this->client->connect();
        $this->assertTrue($this->client->isConnected());
    }

    public function testRequest()
    {

    }
}
