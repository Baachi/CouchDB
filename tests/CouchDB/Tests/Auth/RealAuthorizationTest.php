<?php
namespace CouchDB\Tests\Http;

use CouchDB\Tests\TestCase;
use CouchDB\Http;
use CouchDB\Auth;


class RealAuthorizationTest extends TestCase
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

    /**
     * @dataProvider authAdaptersProvider
     */
    public function testUsesAuthAdapter($auth)
    {
        $client = self::client();
        $this->assertEquals(401, $client->request('_config')->getStatusCode());
        $client->connect($auth);
        $this->assertEquals(200, $client->request('_config')->getStatusCode());
        $this->assertEquals(200, $client->request('_config')->getStatusCode());
    }

    public static function client()
    {
        return new Http\StreamClient();
    }

    public static function authAdaptersProvider()
    {
        return array(
            array(new Auth\Cookie(self::LOGIN, self::PWD)),
            array(new Auth\Basic(self::LOGIN, self::PWD))
        );
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
        $conn->getClient()->connect(new Auth\Cookie(self::LOGIN, self::PWD));
        $conn->getClient()->request('_config/admins/' . self::LOGIN,
            Http\ClientInterface::METHOD_DELETE);
    }

}
