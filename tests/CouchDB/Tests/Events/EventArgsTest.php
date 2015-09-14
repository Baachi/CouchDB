<?php

namespace CouchDB\Tests\Events;

use CouchDB\Events\EventArgs;
use CouchDB\Tests\TestCase;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class EventArgsTest extends TestCase
{
    public function testGetInvoker()
    {
        $invoker = new Dummy();
        $args = new EventArgs($invoker);
        $this->assertEquals($invoker, $args->getInvoker());
        $this->assertEquals('bar', $args->getInvoker()->foo);
        $invoker->foo = 'foobar';
        $this->assertEquals('foobar', $args->getInvoker()->foo);
    }

    public function testGetData()
    {
        $data = 'foo';
        $args = new EventArgs(new Dummy(), $data);
        $this->assertEquals('foo', $args->getData());
    }
}

class Dummy
{
    public $foo = 'bar';
}
