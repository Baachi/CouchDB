<?php
namespace CouchDB;

use CouchDB\Http\ClientInterface;
use CouchDB\Events\EventArgs;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class Database
{
    /**
     * @var ClientInterface
     */
    protected $client;

    /**
     * @var EventDispatcher
     */
    protected $dispatcher;

    /**
     * @var string
     */
    protected $name;

    public function __construct($name, ClientInterface $client, EventDispatcher $dispatcher)
    {
        $this->client = $client;
        $this->dispatcher = $dispatcher;
        $this->name = $name;
    }

    public function initialize()
    {
        if ($this->client->isConnected()) {
            return;
        }

        if ($this->dispatcher->hasListeners(Events::preConnect)) {
            $this->dispatcher->dispatch(Events::preConnect, new EventArgs($this));
        }

        $this->client->connect();

        if ($this->dispatcher->hasListeners(Events::postConnect)) {
            $this->dispatcher->dispatch(Events::postConnect, new EventArgs($this));
        }
    }

    public function find($id)
    {

    }

    public function findAllDocs()
    {

    }

    public function info()
    {

    }

    public function createQueryBuilder()
    {

    }

    public function getClient()
    {
        return $this->client;
    }

    public function setClient(ClientInterface $client)
    {
        $this->client = $client;
        $this->initialize();
    }

    public function getName()
    {
        return $this->name;
    }
}
