<?php
namespace CouchDB\Tests\Http;

use CouchDB\Tests\TestCase;
use CouchDB\Http;
use CouchDB\Auth;


class RealCookieAuthorizationTest extends TestCase
{
    const LOGIN = 'test';
    const PWD = '123';

    protected function setUp()
    {
        $this->createServerAdmin();
    }

    protected function tearDown()
    {
        $this->deleteServerAdmin();
    }

    public function testUsesAuthAdapter()
    {
        $client = self::client();
        $this->assertEquals(302, $client->request('_config')->getStatusCode());
        $client->connect(self::authAdapter());
        $this->assertEquals(200, $client->request('_config')->getStatusCode());
        $this->assertEquals(200, $client->request('_config')->getStatusCode());
    }

    public static function client()
    {
        return new Http\StreamClient();
    }

    private static function authAdapter()
    {
        return new Auth\Cookie(self::LOGIN, self::PWD);
    }

    private function createServerAdmin()
    {
        $this->createTestConnection()->getClient()->request(
            '_config/admins/' . self::LOGIN,
            Http\ClientInterface::METHOD_PUT,
                '"' . self::PWD . '"'
        );
    }

    private function deleteServerAdmin()
    {
        $conn = $this->createTestConnection();
        $conn->getClient()->connect(self::authAdapter());
        $conn->getClient()->request('_config/admins/' . self::LOGIN,
            Http\ClientInterface::METHOD_DELETE);
    }

}
