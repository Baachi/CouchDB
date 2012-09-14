<?php
namespace CouchDB\Auth;

use CouchDB\Tests\TestCase;

class BasicTest extends TestCase
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
        return new Basic('test', '123');
    }
}
