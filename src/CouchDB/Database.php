<?php
namespace CouchDB;

use CouchDB\Encoder\JSONEncoder;
use CouchDB\Exception\Exception;
use GuzzleHttp\ClientInterface;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class Database
{
    /**
     * @var Connection
     */
    private $conn;

    /**
     * @var string
     */
    private $name;

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @param string          $name The database name.
     * @param Connection      $conn The current connection.
     * @param ClientInterface $client The client
     */
    public function __construct($name, Connection $conn, ClientInterface $client)
    {
        $this->conn = $conn;
        $this->name = $name;
        $this->client = $client;
    }

    /**
     * Gets the current connection
     *
     * @return Connection
     */
    public function getConnection()
    {
        return $this->conn;
    }

    /**
     * Find a document by a id.
     *
     * @param string $id

     * @return mixed

     * @throws Exception If the document doesn't exists.
     */
    public function find($id)
    {
        $response = $this->client->request('GET', sprintf('/%s/%s', $this->name, $id));
        $json     = (string) $response->getBody();

        if (404 === $response->getStatusCode()) {
            throw new Exception('Document does not exist');
        }

        return JSONEncoder::decode($json);
    }

    /**
     * Find all documents from the database.
     *
     * @param integer|null $limit
     * @param string|null $startKey
     *
     * @return mixed
     */
    public function findAll($limit = null, $startKey = null)
    {
        $path = "/{$this->name}/_all_docs?include_docs=true";

        if (null !== $limit) {
            $path .= '&limit=' . (integer) $limit;
        }
        if (null !== $startKey) {
            $path .= '&startkey=' . (string) $startKey;
        }

        $json = (string) $this->client->request('GET', $path)->getBody();
        $docs = JSONEncoder::decode($json);

        return $docs;
    }

    /**
     * Find a documents by a id
     *
     * @param array        $ids
     * @param null|integer $limit
     * @param null|integer $offset
     *
     * @return array|null
     */
    public function findDocuments(array $ids, $limit = null, $offset = null)
    {
        $path = "/{$this->name}/_all_docs?include_docs=true";

        if (null !== $limit) {
            $path .= '&limit=' . (integer) $limit;
        }
        if (null !== $offset) {
            $path .= '&skip=' . (integer) $offset;
        }

        $response = $this->client->request('POST', $path, [
            'body' => JSONEncoder::encode(['keys' => $ids]),
            'headers' => ['Content-Type' => 'application/json']
        ]);

        return JSONEncoder::decode((string) $response->getBody());
    }

    /**
     * Insert a new document.
     *
     * @param array $doc
     *
     * @throws Exception
     */
    public function insert(array &$doc)
    {
        if (isset($doc['_id'])) {
            $clone = $doc;
            unset($clone['_id']);

            $response = $this->client->request('PUT', "/{$this->name}/{$doc['_id']}", [
                'body' => JSONEncoder::encode($clone),
                'headers' => ['Content-Type' => 'application/json']
            ]);
        } else {
            $response = $this->client->request('POST', "/{$this->name}/", [
                'body' => JSONEncoder::encode($doc),
                'headers' => ['Content-Type' => 'application/json']
            ]);
        }

        if (201 !== $response->getStatusCode()) {
            throw new Exception('Unable to save document');
        }

        $value = JSONEncoder::decode((string) $response->getBody());

        $doc['_id'] = $value['id'];
        $doc['_rev'] = $value['rev'];
    }

    /**
     * Updates a document
     *
     * @param  string            $id  The id from the document
     * @param  array             $doc A reference from the document
     *
     * @throws Exception
     */
    public function update($id, array &$doc)
    {
        $json = JSONEncoder::encode($doc);

        $response = $this->client->request('PUT', "/{$this->name}/{$id}", [
            'body' => $json,
            'headers' => ['Content-Type' => 'application/json']
        ]);

        if (201 !== $response->getStatusCode()) {
            throw new Exception('Unable to save document');
        }

        $value = JSONEncoder::decode((string) $response->getBody());

        $doc['_id'] = $value['id'];
        $doc['_rev'] = $value['rev'];
    }

    /**
     * Deletes a document
     *
     * @param string $id
     * @param string $rev
     *
     * @return bool
     * @throws \RuntimeException
     */
    public function delete($id, $rev)
    {
        $response = $this->client->request('DELETE', "/{$this->name}/{$id}?rev={$rev}");

        if (200 !== $response->getStatusCode()) {
            throw new Exception(sprintf('Unable to delete %s', $id));
        }

        return true;
    }

    /**
     * Creates a batch updater
     *
     * @return Util\BatchUpdater
     */
    public function createBatchUpdater()
    {
        return new Util\BatchUpdater($this->client, $this);
    }

    /**
     * Return the database informations
     *
     * @return array
     */
    public function getInfo()
    {
        $json = (string) $this->client->request('GET', "/{$this->name}/")->getBody();

        return JSONEncoder::decode($json);
    }

    /**
     * Return informations about the last changes from the database
     *
     * @return array
     *
     * @throws \RuntimeException If the request was not successfull
     */
    public function getChanges()
    {
        $response = $this->client->request('GET', "/{$this->name}/_changes");

        if (200 !== $response->getStatusCode()) {
            throw new Exception('Request wasn\'t successfull');
        }

        return JSONEncoder::decode((string) $response->getBody());
    }

    /**
     * Return the database name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
