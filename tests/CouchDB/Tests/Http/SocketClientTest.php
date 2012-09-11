<?php
namespace CouchDB\Tests\Http;

use CouchDB\Tests\TestCase;
use CouchDB\Http;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class SocketClientTest extends TestCase
{
    protected function setUp()
    {
        $this->client = $this->getTestClient();
    }

    public function testProcessRawSocketResponse()
    {
        $client = new Http\SocketClient();
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
            $c = new Http\SocketClient();
        }
        return $c;
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
