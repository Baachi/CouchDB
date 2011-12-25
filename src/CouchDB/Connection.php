<?php
namespace CouchDB;

use CouchDB\Http\ClientInterface;
use CouchDB\Events\EventArgs;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class Connection
{
    private $client;

    private $dispatcher;

    public function __construct(ClientInterface $client, EventDispatcher $dispatcher = null)
    {
        $this->client = $client;
        $this->dispatcher = $dispatcher ?: new EventDispatcher();
    }

    public function initialize()
    {
        if (!$this->client->isConnected()) {
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

    public function isConnected()
    {
        return $this->client->isConnected();
    }

    public function version()
    {
        $this->initialize();
        return $this->client->request('/');
    }

    public function listDatabases()
    {
        $this->initialize();
        return $this->client->request('/_all_dbs');
    }

    public function dropDatabase($name)
    {
        $this->initialize();
        if ($this->dispatcher->hasListeners(Events::preDropDatabase)) {
            $this->dispatcher->dispatch(Events::preDropDatabase, new EventArgs($this, $name));
        }

        $retVal = $this->client->request("/{$name}", ClientInterface::METHOD_DELETE)->getContent();

        if ($this->dispatcher->hasListeners(Events::postDropDatabase)) {
            $this->dispatcher->dispatch(Events::postDropDatabase, new EventArgs($this, $name));
        }

        return $retVal;
    }

    public function selectDatabase($name)
    {
        $this->initialize();
        $db = $this->client->request("/{$name}");
        return $db;
    }

    public function __get($name)
    {
        return $this->selectDatabase($name);
    }

    public function __unset($name)
    {
        return $this->dropDatabase($name);
    }
}
