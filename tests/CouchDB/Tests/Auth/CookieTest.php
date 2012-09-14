<?php
namespace CouchDB\Auth;

use CouchDB\Http;

class CookieTest extends \PHPUnit_Framework_TestCase
{

    public function testIsAuthAdapter()
    {
        $this->assertInstanceOf('\CouchDB\Auth\AuthInterface', self::auth());
    }

    public function testDoesCookieAuthorization()
    {
        $client = $this->getMock('CouchDB\Http\ClientInterface');

        $client->expects($this->once())
                ->method('request')
                ->with(
                    '/_session',
                    Http\ClientInterface::METHOD_POST,
                    'name=login&password=pwd',
                    array('Content-Type' => 'application/x-www-form-urlencoded')
                );

        self::auth()->authorize($client);
    }

    public function testStoresAuthorizationCookie()
    {
        $auth = self::auth();
        $auth->authorize($this->successfullyAuthorizedClient());
        $this->assertEquals(
            array('Cookie' => 'AuthSession=eGlhZzo1MDRENENEMzqi0aDXLCTNmbz4Em7C7qS-XFW3rA'),
            $auth->getHeaders()
        );
    }

    private static function auth()
    {
        return new Cookie('login', 'pwd');
    }

    private function successfullyAuthorizedClient()
    {
        $client = $this->getMock('CouchDB\Http\ClientInterface');
        $client
                ->expects($this->any())
                ->method('request')
                ->will($this->returnValue(
            new Http\Response\Response(200, '{"ok":true}', array(
                'set-cookie' => ' AuthSession=eGlhZzo1MDRENENEMzqi0aDXLCTNmbz4Em7C7qS-XFW3rA; Version=1; Path=/; HttpOnly'
            ))
        ));

        return $client;
    }

}
