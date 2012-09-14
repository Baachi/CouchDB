<?php
namespace CouchDB\Logging;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class StackLogger implements LoggingInterface
{
    /**
     * @var SplStack
     */
    protected $stack;

    public function __construct()
    {
        $this->stack = new \SplStack();
    }

    /**
     * Return the stack
     *
     * @return SplStack
     */
    public function getStack()
    {
        return $this->stack;
    }

    /**
     * Logs a message
     *
     * @param array $message
     */
    public function log(array $message)
    {
        $this->stack->push($message);
    }

}
