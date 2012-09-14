<?php
namespace CouchDB\Tests\Http;

use CouchDB\Http\LoggingClient;
use CouchDB\Http\Response\Response;
use CouchDB\Tests\TestCase;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class LoggingClientTest extends TestCase
{
    /**
     * @var \CouchDB\Http\LoggingClient
     */
    protected $client;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockClient;

    protected function setUp()
    {
        $this->mockClient = $this->getMock('CouchDB\\Http\\ClientInterface');
        $this->mockClient
            ->expects($this->any())
            ->method('request')
            ->will($this->returnCallback(function() {
                usleep(1);

                return new Response(200, 'test', array());
            }
        ));

        $this->client = new LoggingClient($this->mockClient);
    }

    public function testIsConnected()
    {
        $this->mockClient->expects($this->once())
            ->method('isConnected')
            ->will($this->returnValue(true));

        $this->assertTrue($this->client->isConnected());
    }

    public function testConnect()
    {
        $this->mockClient->expects($this->once())
            ->method('connect')
            ->will($this->returnValue(null));

        $this->assertNull($this->client->connect());
    }

    public function testRequestWithMethodGet()
    {
        $response = $this->client->request('/');
        $this->assertEquals('test', $response->getContent());
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEmpty($response->getHeaders());
        $this->assertInternalType('array', $response->getHeaders());

        $stack = $this->client->getStack();
        $current = $stack[0];

        $this->assertCount(1, $stack);
        $expected = array(
            'duration', 'request_method', 'request_path', 'request_data',
            'request_headers', 'response_headers', 'response_status', 'response_body'
        );
        $this->assertEquals($expected, array_keys($current) );

        $this->assertGreaterThan(0, $current['duration']);
        $this->assertEquals('GET', $current['request_method']);
        $this->assertEquals('/', $current['request_path']);
        $this->assertEquals('', $current['request_data']);
        $this->assertEquals(array(), $current['request_headers']);
        $this->assertEquals(array(), $current['response_headers']);
        $this->assertEquals(200, $current['response_status']);
        $this->assertEquals('test', $current['response_body']);
    }

    public function testGetTotalDuration()
    {
        $this->assertEquals(0, $this->client->getTotalDuration());

        for ($i = 0; $i < 10; $i++) {
            $this->client->request('/');
        }

        $this->assertGreaterThan(0, $this->client->getTotalDuration());
    }
}
