<?php
namespace CouchDB\Tests\Http;

use CouchDB\Http;

/**
 * @author Maxim Gnatenko <mgnatenko@gmail.com>
 */
class StreamClientTest extends \PHPUnit_Framework_TestCase
{

    public function testIsNotConnectedInitially()
    {
        $this->assertFalse(self::client()->isConnected());
    }

    public function testConnect()
    {
        $this->assertTrue(self::client()->connect()->isConnected());
    }

    public function testGetRequestWorks()
    {
        $this->assertEquals(
            200,
            self::client()->connect()->request('/')->getStatusCode()
        );
    }

    private static function client()
    {
        return new Http\StreamClient();
    }

}
