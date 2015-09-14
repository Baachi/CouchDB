<?php

namespace CouchDB\Tests;

use CouchDB\Connection;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class TestCase extends \PHPUnit_Framework_TestCase
{
    protected $mock;

    protected $client;

    protected $connection;

    protected function setUp()
    {
        $this->mock = new MockHandler();
        $this->client = new Client([
            'handler'     => HandlerStack::create($this->mock),
            'http_errors' => false,
        ]);
        $this->connection = new Connection($this->client);
    }
}
