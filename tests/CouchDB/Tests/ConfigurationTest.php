<?php
namespace CouchDB\Tests;

use CouchDB\Configuration;
use CouchDB\Encoder\NativeEncoder;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class ConfigurationTest extends TestCase
{
    protected function setUp()
    {
        $this->config = new Configuration();
    }

    public function testGetEncoder()
    {
        $this->assertInstanceOf('\\CouchDB\\Encoder\\EncoderInterface', $this->config->getEncoder());
    }

    public function testSetEncoder()
    {
        $encoder = new NativeEncoder();
        $this->config->setEncoder($encoder);
        $this->assertEquals($encoder, $this->config->getEncoder());
    }
}
