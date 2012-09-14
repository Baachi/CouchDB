<?php
namespace CouchDB\Tests\Http;

use CouchDB\Tests\TestCase;
use CouchDB\Http;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class SocketClientTest extends TestCase
{

    protected function tearDown()
    {
        self::client()->connect()->request('/test', 'DELETE');
    }

    public function testProcessRawSocketResponse()
    {
        $client = self::client();
        $client->setTestConnection(self::connectionMock());

        $this->assertEquals(
            new Http\Response\Response(
                200,
                '["_users","test","ttt"]',
                array(
                    'server' => 'CouchDB/1.0.1 (Erlang OTP/R14B)',
                    'date' => 'Tue, 11 Sep 2012 10:34:40 GMT',
                    'content-type' => 'text/plain;charset=utf-8',
                    'content-length' => '24',
                    'cache-control' => 'must-revalidate',
                )
            ),
            $client->request('')
        );

    }

    public function testIsNotConnectedInitially()
    {
        $this->assertFalse(self::client()->isConnected());
    }

    public function testConnect()
    {
        $this->assertTrue(self::client()->connect()->isConnected());
    }

    public function testPutRequest()
    {
        $response = self::client()->connect()->request('/test', 'PUT');
        $this->assertTrue($response->isSuccessful());
    }

    public function testPostRequest()
    {
        $response = self::client()->connect()->request('/_all_dbs', 'GET');
        $this->assertInstanceOf('\CouchDB\Http\Response\ResponseInterface', $response);
        $this->assertTrue($response->isSuccessful());
        $this->assertInternalType('string', $response->getContent());
    }

    public function testCanUseSameConnectionForMultipleRequests()
    {
        $this->markTestIncomplete('It doesnt work by some reason');

        $client = self::client();
        $client->connect();
        $client->request('/_all_dbs', 'GET');
        $client->request('/_all_dbs', 'GET');
        $client->request('/_users', 'GET');
        $client->request('/', 'GET');
    }

    private static function client()
    {
        return new Http\SocketClient();
    }

    private static function connectionMock()
    {
        $h = fopen('php://memory', 'rw');
        fwrite($h, self::coachDbFakeResponse());
        rewind($h);

        return $h;
    }

    private static function coachDbFakeResponse()
    {
        return <<<'TEXT'
.......placeholder to write request...................
HTTP/1.1 200 OK
Server: CouchDB/1.0.1 (Erlang OTP/R14B)
Date: Tue, 11 Sep 2012 10:34:40 GMT
Content-Type: text/plain;charset=utf-8
Content-Length: 24
Cache-Control: must-revalidate

["_users","test","ttt"]

TEXT;

    }
}
