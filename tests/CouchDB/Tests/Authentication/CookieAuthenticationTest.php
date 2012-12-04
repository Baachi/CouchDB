<?php
namespace CouchDB\Tests\Authentication;

use CouchDB\Tests\TestCase;
use CouchDB\Authentication\CookieAuthentication;
use CouchDB\Http\ClientInterface;
use CouchDB\Http\Response\Response;

class CookieAuthenticationTest extends TestCase
{
    public function testDoesCookieAuthorization()
    {
        $adapter = new CookieAuthentication('johndoe', 'secret');
        $client = $this->getMock('CouchDB\Http\ClientInterface');

        $client->expects($this->once())
                ->method('request')
                ->with(
                    '/_session',
                    ClientInterface::METHOD_POST,
                    'name=johndoe&password=secret',
                    array('Content-Type' => 'application/x-www-form-urlencoded')
                );

        $this->assertEquals($adapter, $adapter->authorize($client));
    }

    public function testStoresAuthorizationCookie()
    {
        $auth = new CookieAuthentication('login', 'secret');

        $auth->authorize($this->successfullyAuthorizedClient());
        $this->assertEquals(
            array('Cookie' => 'AuthSession=eGlhZzo1MDRENENEMzqi0aDXLCTNmbz4Em7C7qS-XFW3rA'),
            $auth->getHeaders()
        );
    }

    private function successfullyAuthorizedClient()
    {
        $adapter = new CookieAuthentication('johndoe', 'secret');
        $client = $this->getMock('CouchDB\Http\ClientInterface');

        $client
            ->expects($this->any())
            ->method('request')
            ->will($this->returnValue(
                new Response(
                    200,
                    '{"ok":true}',
                    array(
                        'set-cookie' => 'AuthSession=eGlhZzo1MDRENENEMzqi0aDXLCTNmbz4Em7C7qS-XFW3rA; Version=1; Path=/; HttpOnly'
                    )
                )
            ));

        return $client;
    }

}
