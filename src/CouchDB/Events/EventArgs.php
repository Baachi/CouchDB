<?php
namespace CouchDB\Events;

use Symfony\Component\EventDispatcher\Event;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class EventArgs extends Event
{
    protected $invoker;

    protected $data;

    public function __construct($invoker, &$data = null)
    {
        $this->invoker = $invoker;
        $this->data    = $data;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getInvoker()
    {
        return $this->invoker;
    }
}
