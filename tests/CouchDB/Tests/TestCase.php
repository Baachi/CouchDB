<?php
namespace CouchDB\Tests;

use CouchDB\Connection;
use CouchDB\Http\BuzzClient;
use Buzz\Browser;
use Buzz\Client\Curl;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class TestCase extends \PHPUnit_Framework_TestCase
{
    public function createTestConnection()
    {
        $client = new Browser(new Curl());
        $conn = new Connection(new BuzzClient($client));
        return $conn;
    }

    public function createTestDatabase($name = 'test')
    {
        return $this->createTestConnection()->createDatabase($name);
    }
}
