<?php
namespace CouchDB;

use CouchDB\Events\EventArgs;
use CouchDB\Encoder\JSONEncoder;
use CouchDB\Exception\InvalidDatabasenameException;
use CouchDB\Exception\Exception;

use Doctrine\Common\EventManager;
use GuzzleHttp\ClientInterface;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class Connection
{
    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var EventManager
     */
    private $eventManager;

    /**
     * @param ClientInterface $client
     * @param EventManager    $dispatcher
     */
    public function __construct(ClientInterface $client, EventManager $dispatcher = null)
    {
        $this->client = $client;
        $this->eventManager = $dispatcher ?: new EventManager();
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
     * Get the couchdb version
     *
     * @return string
     */
    public function version()
    {
        $json  = (string) $this->client->request('GET', '/')->getBody();
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
        $json      = (string) $this->client->request('GET', '/_all_dbs')->getBody();
        $databases = JSONEncoder::decode($json);

        return $databases;
    }

    /**
     * Drop a database
     *
     * @param  string $name
     *
     * @return bool
     */
    public function dropDatabase($name)
    {
        if ($this->eventManager->hasListeners(Events::preDropDatabase)) {
            // @codeCoverageIgnoreStart
            $this->eventManager->dispatchEvent(Events::preDropDatabase, new EventArgs($this, $name));
            // @codeCoverageIgnoreEnd
        }

        $response = $this->client->request('DELETE', sprintf('/%s/', urlencode($name)));

        if (404 === $response->getStatusCode()) {
            throw new Exception(sprintf('The database "%s" does not exist', $name));
        }

        $json = (string) $response->getBody();
        $status = JSONEncoder::decode($json);

        if ($this->eventManager->hasListeners(Events::postDropDatabase)) {
            // @codeCoverageIgnoreStart
            $this->eventManager->dispatchEvent(Events::postDropDatabase, new EventArgs($this, $name));
            // @codeCoverageIgnoreEnd
        }

        return isset($status['ok']) && $status['ok'] === true;
    }

    /**
     * Select a database.
     *
     * @param string $name
     *
     * @return Database
     *
     * @throws Exception If the database doesn't exists.
     */
    public function selectDatabase($name)
    {
        $response = $this->client->request('GET', sprintf("/%s/", $name));

        if (404 === $response->getStatusCode()) {
            throw new Exception(sprintf('The database "%s" does not exist', $name));
        }

        return $this->wrapDatabase($name);
    }

    /**
     * Check if a database exists.
     *
     * @param string $name The database name
     *
     * @return bool
     */
    public function hasDatabase($name)
    {
        $response = $this->client->request(
            'GET',
            sprintf("/%s/", urlencode($name))
        );

        return 404 !== $response->getStatusCode();
    }

    /**
     * Creates a new database.
     *
     * @param string $name The database name
     *
     * @return Database
     *
     * @throws Exception If the database could not be created.
     */
    public function createDatabase($name)
    {
        if (preg_match('@[^a-z0-9\_\$\(\)+\-]@', $name)) {
            throw new InvalidDatabasenameException(sprintf(
                'The database name %s is invalid. The database name must match the following pattern (a-z0-9_$()+-)',
                $name
            ));
        }

        if ($this->eventManager->hasListeners(Events::preCreateDatabase)) {
            // @codeCoverageIgnoreStart
            $this->eventManager->dispatchEvent(Events::preCreateDatabase, new EventArgs($this, $name));
            // @codeCoverageIgnoreEnd
        }

        $response  = $this->client->request('PUT', sprintf('/%s', $name));

        if (412 === $response->getStatusCode()) {
            throw new Exception(sprintf('The database "%s" already exist', $name));
        }

        $json = (string) $response->getBody();
        $value = JSONEncoder::decode($json);

        if (isset($value['error'])) {
            throw new Exception(sprintf('[%s] Failed to create database "%s". (%s)', $value['error'], $name, $value['reason']));
        }

        $database = $this->wrapDatabase($name);

        if ($this->eventManager->hasListeners(Events::postCreateDatabase)) {
            // @codeCoverageIgnoreStart
            $this->eventManager->dispatchEvent(Events::postCreateDatabase, new EventArgs($database));
            // @codeCoverageIgnoreEnd
        }

        return $database;
    }

    /**
     * Gets the database
     *
     * @param string $name The database name
     *
     * @return Database
     *
     * @codeCoverageIgnore
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
     *
     * @codeCoverageIgnore
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
     *
     * @codeCoverageIgnore
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
    private function wrapDatabase($name)
    {
        return new Database($name, $this, $this->client);
    }
}
