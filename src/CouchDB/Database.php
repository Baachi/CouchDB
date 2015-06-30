<?php
namespace CouchDB;

use CouchDB\Http\ClientInterface;
use CouchDB\Encoder\JSONEncoder;

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
     * Constructor
     *
     * @param string $name
     * @param Connection $conn
     */
    public function __construct($name, Connection $conn)
    {
        $this->conn = $conn;
        $this->name = $name;
    }

    /**
     * Find a document by a id
     *
     * @param $id
     * @return mixed
     * @throws \RuntimeException
     */
    public function find($id)
    {
        $this->conn->initialize();

        $response = $this->conn->getClient()->request("/{$this->name}/{$id}");
        $json     = $response->getContent();

        if (404 === $response->getStatusCode()) {
            throw new \RuntimeException('Document does not exist');
        }

        return JSONEncoder::decode($json);
    }

    /**
     * Find all documents from the database
     *
     * @param  null  $limit
     * @param  null  $startKey
     * @return mixed
     */
    public function findAll($limit = null, $startKey = null)
    {
        $this->conn->initialize();

        $path = "/{$this->name}/_all_docs?include_docs=true";

        if (null !== $limit) {
            $path .= '&limit=' . (integer) $limit;
        }
        if (null !== $startKey) {
            $path .= '&startkey=' . (string) $startKey;
        }

        $json = $this->conn->getClient()->request($path)->getContent();
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
        $this->conn->initialize();

        $path = "/{$this->name}/_all_docs?include_docs=true";

        if (null !== $limit) {
            $path .= '&limit=' . (integer) $limit;
        }
        if (null !== $offset) {
            $path .= '&skip=' . (integer) $offset;
        }

        $json = JSONEncoder::encode(array('keys' => $ids));

        $response = $this->conn->getClient()->request(
            $path,
            ClientInterface::METHOD_POST,
            $json,
            array('Content-Type' => 'application/json')
        );

        $value = JSONEncoder::decode($response->getContent());

        return $value;
    }

    /**
     * Insert a new document.
     *
     * @param  array             $doc
     * @throws \RuntimeException
     */
    public function insert(array &$doc)
    {
        $this->conn->initialize();

        if (isset($doc['_id'])) {
            $clone = $doc;
            unset($clone['_id']);

            $response = $this->conn->getClient()->request(
                "/{$this->name}/{$doc['_id']}",
                ClientInterface::METHOD_PUT,
                JSONEncoder::encode($clone),
                array('Content-Type' => 'application/json')
            );
        } else {
            $response = $this->conn->getClient()->request(
                "/{$this->name}/",
                ClientInterface::METHOD_POST,
                JSONEncoder::encode($doc),
                array('Content-Type' => 'application/json')
            );
        }

        if (201 !== $response->getStatusCode()) {
            throw new \RuntimeException('Unable to save document');
        }

        $value  = JSONEncoder::decode($response->getContent());
        $id     = $value['id'];
        $rev    = $value['rev'];

        $doc['_id'] = $id;
        $doc['_rev'] = $rev;
    }

    /**
     * Updates a document
     *
     * @param  string            $id  The id from the document
     * @param  array             $doc A reference from the document
     * @return bool
     * @throws \RuntimeException
     */
    public function update($id, array &$doc)
    {
        $this->conn->initialize();

        $json = JSONEncoder::encode($doc);

        $response = $this->getConnection()->getClient()->request(
            "/{$this->name}/{$id}",
            ClientInterface::METHOD_PUT,
            $json,
            array('Content-Type' => 'application/json')
        );

        if (201 !== $response->getStatusCode()) {
            throw new \RuntimeException('Unable to save document');
        }

        $value = JSONEncoder::decode($response->getContent());

        $doc['_id'] = $value['id'];
        $doc['_rev'] = $value['rev'];

        return true;
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
        $this->conn->initialize();
        $response = $this->conn->getClient()->request("/{$this->name}/{$id}?rev={$rev}", ClientInterface::METHOD_DELETE);

        if (200 !== $response->getStatusCode()) {
            throw new \RuntimeException(sprintf('Unable to delete %s', $id));
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
        return new Util\BatchUpdater($this->conn->getClient(), $this);
    }

    /**
     * Return the database informations
     *
     * @return array
     */
    public function getInfo()
    {
        $this->conn->initialize();
        $json = $this->conn->getClient()->request("/{$this->name}/")->getContent();
        $info = JSONEncoder::decode($json);

        return $info;
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
        $this->conn->initialize();
        $response = $this->conn->getClient()->request("/{$this->name}/_changes");

        if (false === $response->isSuccessful()) {
            throw new \RuntimeException('Request wasn\'t successfull');
        }

        return JSONEncoder::decode($response->getContent());
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
