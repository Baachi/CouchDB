<?php
namespace CouchDB;

use CouchDB\Http\ClientInterface;
use CouchDB\Events\EventArgs;
use CouchDB\Encoder\JSONEncoder;
use Doctrine\Common\EventManager;
use CouchDB\Authentication\AuthenticationInterface;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class Connection
{
    /**
     * @var AuthenticationInterface
     */
    private $authAdapter;

    /**
     * @var \CouchDB\Http\ClientInterface
     */
    private $client;

    /**
     * @var \Doctrine\Common\EventManager
     */
    private $eventManager;

    /**
     * Constructor
     *
     * @param ClientInterface         $client
     * @param EventManager            $dispatcher
     * @param AuthenticationInterface $authAdapter
     */
    public function __construct(ClientInterface $client, EventManager $dispatcher = null, AuthenticationInterface $authAdapter = null)
    {
        $this->client = $client;
        $this->eventManager = $dispatcher ?: new EventManager();
        $this->authAdapter = $authAdapter;
    }

    /**
     * Return the HTTP Client
     *
     * @return Http\ClientInterface
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Set the client
     *
     * @param Http\ClientInterface $client
     */
    public function setClient(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * Return the event dispatcher
     *
     * @return \Doctrine\Common\EventManager
     */
    public function getEventManager()
    {
        return $this->eventManager;
    }

    /**
     * Initialized the client
     */
    public function initialize()
    {
        if ($this->client->isConnected()) {
            return;
        }

        if ($this->eventManager->hasListeners(Events::preConnect)) {
            $this->eventManager->dispatchEvent(Events::preConnect, new EventArgs($this));
        }

        $this->client->connect($this->authAdapter);

        if ($this->eventManager->hasListeners(Events::postConnect)) {
            $this->eventManager->dispatchEvent(Events::postConnect, new EventArgs($this));
        }
    }

    /**
     * Check if the client is connected to the couchdb server
     *
     * @return bool
     */
    public function isConnected()
    {
        return $this->client->isConnected();
    }

    /**
     * Get the couchdb version
     *
     * @return string
     */
    public function version()
    {
        $this->initialize();
        $json  = $this->client->request('/')->getContent();
        $value = JSONEncoder::decode($json);

        return $value['version'];
    }

    /**
     * Show all databases
     *
     * @return array
     */
    public function listDatabases()
    {
        $this->initialize();
        $json      = $this->client->request('/_all_dbs')->getContent();
        $databases = JSONEncoder::decode($json);

        return $databases;
    }

    /**
     * Drop a database
     *
     * @param  string $name
     * @return bool
     */
    public function dropDatabase($name)
    {
        $this->initialize();
        if ($this->eventManager->hasListeners(Events::preDropDatabase)) {
            $this->eventManager->dispatchEvent(Events::preDropDatabase, new EventArgs($this, $name));
        }

        $name = urlencode($name);

        $response = $this->client->request("/{$name}/", ClientInterface::METHOD_DELETE);

        if (404 === $response->getStatusCode()) {
            throw new \RuntimeException(sprintf('The database %s does not exist', $name));
        }

        $json = $response->getContent();
        $status = JSONEncoder::decode($json);

        if ($this->eventManager->hasListeners(Events::postDropDatabase)) {
            $this->eventManager->dispatchEvent(Events::postDropDatabase, new EventArgs($this, $name));
        }

        return isset($status['ok']) && $status['ok'] === true;
    }

    /**
     * Select a database
     *
     * @param  string   $name
     * @return Database
     */
    public function selectDatabase($name)
    {
        $this->initialize();

        $name = urlencode($name);

        $response = $this->client->request("/{$name}");
        if (404 === $response->getStatusCode()) {
            throw new \RuntimeException(sprintf('The database %s does not exist', $name));
        }

        $db = $this->wrapDatabase($name);

        return $db;
    }

    /**
     * Check if the database exist
     *
     * @param  string $name The database name
     * @return bool
     */
    public function hasDatabase($name)
    {
        $this->initialize();
        $name = urlencode($name);
        $response = $this->client->request("/{$name}");
        if (404 === $response->getStatusCode()) {
            return false;
        }

        return true;
    }

    /**
     * Create a new database
     *
     * @param  string            $name
     * @return Database
     * @throws \RuntimeException If the database could not be created
     */
    public function createDatabase($name)
    {
        if (preg_match('@[^a-z0-9\_\$\(\)+\-]@', $name)) {
            throw new \RuntimeException(sprintf('The database name %s is invalid. The database name must match the following pattern (a-z0-9_$()+-', $name));
        }

        $this->initialize();

        $name = urlencode($name);

        if ($this->eventManager->hasListeners(Events::preCreateDatabase)) {
            $this->eventManager->dispatchEvent(Events::preCreateDatabase, new EventArgs($this, $name));
        }

        $response  = $this->client->request("/{$name}/", ClientInterface::METHOD_PUT);

        if (412 === $response->getStatusCode()) {
            throw new \RuntimeException(sprintf('The database %s already exist', $name));
        }

        $json      = $response->getContent();
        $value     = JSONEncoder::decode($json);

        if (isset($value['error'])) {
            throw new \RuntimeException(sprintf('[%s] Failed to create database %s. (%s)', $value['error'], $name, $value['reason']));
        }

        $database = $this->wrapDatabase($name);

        if ($this->eventManager->hasListeners(Events::postCreateDatabase)) {
            $this->eventManager->dispatchEvent(Events::postCreateDatabase, new EventArgs($database));
        }

        return $database;
    }

    /**
     * Gets the database
     *
     * @param  string   $name
     * @return Database
     */
    public function __get($name)
    {
        return $this->selectDatabase($name);
    }

    /**
     * Drop a database
     *
     * @param  string $name
     * @return bool
     */
    public function __unset($name)
    {
        return $this->dropDatabase($name);
    }

    /**
     * Check if the database exist
     *
     * @param  string $name
     * @return bool
     */
    public function __isset($name)
    {
        return $this->hasDatabase($name);
    }

    /**
     * Wraps the database to a object
     *
     * @param  string   $name
     * @return Database
     */
    protected function wrapDatabase($name)
    {
        return new Database($name, $this);
    }
}
