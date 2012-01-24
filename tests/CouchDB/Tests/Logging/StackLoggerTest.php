<?php
namespace CouchDB\Tests\Logging;

use CouchDB\Tests\TestCase;
use CouchDB\Logging\StackLogger;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class StackLoggerTest extends TestCase
{
    public function testLog()
    {
        $stackLogger = new StackLogger();
        $stackLogger->log(array(
            'foo' => 'bar',
        ));
        /* @var $stack \SplStack */
        $stack = $stackLogger->getStack();
        $this->assertCount(1, $stack);
        $this->assertFalse($stack->isEmpty());
        $this->assertEquals(array('foo' => 'bar'), $stack->pop());
    }
}
