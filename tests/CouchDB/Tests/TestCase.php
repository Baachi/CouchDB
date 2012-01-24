<?php
namespace CouchDB\Tests;

use CouchDB\Connection;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class TestCase extends \PHPUnit_Framework_TestCase
{
    public function createTestConnection()
    {
        $client = new \CouchDB\Http\SocketClient();
        $conn   = new Connection($client);
        return $conn;
    }

    public function createTestDatabase($name = 'test')
    {
        $conn = $this->createTestConnection();
        if ($conn->hasDatabase($name)) {
            $conn->dropDatabase($name);
        }

        return $conn->createDatabase($name);
    }
}
