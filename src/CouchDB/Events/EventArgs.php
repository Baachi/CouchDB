<?php
namespace CouchDB\Events;

use Doctrine\Common\EventArgs as BaseEventArgs;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class EventArgs extends BaseEventArgs
{
    /**
     * @var object
     */
    protected $invoker;

    /**
     * @var mixed
     */
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
