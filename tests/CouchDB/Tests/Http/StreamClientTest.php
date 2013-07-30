<?php
namespace CouchDB\Tests\Http;

use CouchDB\Http\StreamClient;

/**
 * @author Maxim Gnatenko <mgnatenko@gmail.com>
 */
class StreamClientTest extends \PHPUnit_Framework_TestCase
{
    public function testIsNotConnectedInitially()
    {
        $client = new StreamClient();
        $this->assertFalse($client->isConnected());
    }

    public function testConnect()
    {
        $client = new StreamClient();
        $this->assertEquals($client, $client->connect());
    }
}
