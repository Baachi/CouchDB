<?php
namespace CouchDB;

use CouchDB\Http\ClientInterface;
use CouchDB\Events\EventArgs;
use CouchDB\Encoder\JSONEncoder;
use CouchDB\Exception\InvalidDatabasenameException;
use Doctrine\Common\EventManager;

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
     * @var ClientInterface
     */
    private $client;

    /**
     * @var EventManager
     */
    private $eventManager;

    /**
     * Create a connection instance.
     * 
     * Available options:
     *   - port: The couchdb port
     *   - host: the couchdb host
     *   - username: The username
     *   - password: The password
     *
     * @param array $options Some options
     *
     * @return Connection
     */
    public static function create(array $options = array())
    {
        $options = array_merge(array(
            'port'           => 5984,
            'host'           => 'localhost',
            'client'         => 'socket',
            'authentication' => null,
            'username'       => null,
            'password'       => null,
        ), $options);

        $authAdapter = null;
        $evm = new EventManager();

        switch ($options['client']) {
            case 'stream':
                $client = new Http\StreamClient($options['host'], $options['port']);
                break;
            case 'socket':
                $client = new Http\SocketClient($options['host'], $options['port']);
                break;
            default:
                throw new \RuntimeException(sprintf(
                    'The client option %s does not exist. Supported are "stream" and "socket"',
                    $options['client']
                ));
        }


        if (null !== $options['username']) {
            $client->setOption('username', $options['username']);
            $client->setOption('password', $options['password']);
        }

        return new static($client, $evm, $authAdapter);
    }

    /**
     * Constructor
     *
     * @param ClientInterface         $client
     * @param EventManager            $dispatcher
     * @param AuthenticationInterface $authAdapter
     */
    public function __construct(ClientInterface $client, EventManager $dispatcher = null)
    {
        $this->client = $client;
        $this->eventManager = $dispatcher ?: new EventManager();
    }

    /**
     * Return the HTTP Client
     *
     * @return ClientInterface
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Set the client
     *
     * @param ClientInterface $client
     */
    public function setClient(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * Return the event dispatcher
     *
     * @return EventManager
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

        $this->client->connect();

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

        $response = $this->client->request("/{$name}/");
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
     *
     * @return bool
     */
    public function hasDatabase($name)
    {
        $this->initialize();

        $name = urlencode($name);
        $response = $this->client->request("/{$name}/");
        if (404 === $response->getStatusCode()) {
            return false;
        }

        return true;
    }

    /**
     * Create a new database
     *
     * @param string $name The database name
     *
     * @return Database
     *
     * @throws \RuntimeException If the database could not be created
     */
    public function createDatabase($name)
    {
        if (preg_match('@[^a-z0-9\_\$\(\)+\-]@', $name)) {
            throw new InvalidDatabasenameException(sprintf('The database name %s is invalid. The database name must match the following pattern (a-z0-9_$()+-)', $name));
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
     * @param string $name The database name
     *
     * @return Database
     */
    public function __get($name)
    {
        return $this->selectDatabase($name);
    }

    /**
     * Drop a database.
     *
     * @param string $name The databas ename
     *
     * @return bool
     */
    public function __unset($name)
    {
        return $this->dropDatabase($name);
    }

    /**
     * Check if the database exist
     *
     * @param string $name The database name
     *
     * @return bool
     */
    public function __isset($name)
    {
        return $this->hasDatabase($name);
    }

    /**
     * Wraps the database to a object
     *
     * @param string $name The database name
     *
     * @return Database
     */
    protected function wrapDatabase($name)
    {
        return new Database($name, $this);
    }
}
