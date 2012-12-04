<?php

namespace CouchDB\Tests\Authentication;

use CouchDB\Tests\TestCase;
use CouchDB\Authentication\BasicAuthentication;

class BasicAuthenticationTest extends TestCase
{
    public function testHasBasicAuthHeader()
    {
        $this->assertEquals(
            array('Authorization' => 'Basic dGVzdDoxMjM='),
            self::auth()->getHeaders()
        );
    }

    private static function auth()
    {
        return new BasicAuthentication('test', '123');
    }
}
