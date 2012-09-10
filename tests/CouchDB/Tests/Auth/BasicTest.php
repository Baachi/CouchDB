<?php
namespace CouchDB\Auth;

class BasicTest extends \PHPUnit_Framework_TestCase {

    public function testHasBasicAuthHeader()
    {
        $this->assertEquals(
            array(
                'Authorization' => 'Basic dGVzdDoxMjM='
            ),
            self::auth()->getHeaders()
        );
    }

//--------------------------------------------------------------------------------------------------

    private static function auth()
    {
        return new Basic('test', '123');
    }
}
