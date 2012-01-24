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
     * @var Connection
     */
    protected $conn;

    /**
     * @var string
     */
    protected $name;

    public function __construct($name, Connection $conn)
    {
        $this->conn = $conn;
        $this->name = $name;
    }

    public function getConnection()
    {
        return $this->conn;
    }

    public function setConnection(Connection $conn)
    {
        $this->conn = $conn;
        $this->conn->initialize();
    }

    public function find($id)
    {
        $this->conn->initialize();
        $response = $this->conn->getClient()->request("/{$this->name}/{$id}");
        $json     = $response->getContent();
        $doc      = $this->conn->getConfiguration()->getEncoder()->decode($json);

        if (404 === $response->getStatusCode()) {
            throw new \RuntimeException('Document does not exist');
        }

        return $doc;
    }

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
        $docs = $this->conn->getConfiguration()->getEncoder()->decode($json);

        return $docs;
    }

    public function findDocuments(array $ids, $limit = null, $offset = null)
    {
        $this->conn->initialize();
        $encoder = $this->conn->getConfiguration()->getEncoder();

        $path = "/{$this->name}/_all_docs?include_docs=true";

        if (null !== $limit) {
            $path .= '&limit=' . (integer) $limit;
        }
        if (null !== $offset) {
            $path .= '&skip=' . (integer) $offset;
        }

        $json = $encoder->encode(array('keys' => $ids));

        $response = $this->conn->getClient()->request(
            $path,
            ClientInterface::METHOD_POST,
            $json,
            array('Content-Type' => 'application/json')
        );

        $value = $encoder->decode($response->getContent());
        return $value;
    }

    public function insert(array & $doc)
    {
        $this->conn->initialize();
        $json = $this->conn->getConfiguration()->getEncoder()->encode($doc);

        if (isset($doc['_id'])) {
            $response = $this->conn->getClient()->request(
                "{$this->name}/{$doc['_id']}",
                ClientInterface::METHOD_PUT,
                $json,
                array('Content-Type' => 'application/json')
            );
        } else {
            $response = $this->conn->getClient()->request(
                "{$this->name}/",
                ClientInterface::METHOD_POST,
                $json,
                array('Content-Type' => 'application/json')
            );
        }

        if (201 !== $response->getStatusCode()) {
            throw new \RuntimeException(sprintf(
                'Unable to save %s: Response (%d): %s',
                var_export($doc, true),
                $response->getStatusCode(),
                $response
            ));
        }

        $value = $this->conn->getConfiguration()->getEncoder()->decode($response->getContent());
        $status = $value['ok'];
        $id     = $value['id'];
        $rev    = $value['rev'];

        $doc['_id'] = $id;
        $doc['_rev'] = $rev;
    }

    public function update($id, & $doc)
    {
        $this->conn->initialize();

        $encoder = $this->getConnection()->getConfiguration()->getEncoder();
        $json = $encoder->encode($doc);

        $response = $this->getConnection()->getClient()->request(
            "{$this->name}/{$id}",
            ClientInterface::METHOD_PUT,
            $json,
            array('Content-Type' => 'application/json')
        );

        if (201 !== $response->getStatusCode()) {
            throw new \RuntimeException('Unable to save document');
        }

        $value = $encoder->decode($response->getContent());
        $id = $value['id'];
        $rev = $value['rev'];

        $doc['_id'] = $id;
        $doc['_rev'] = $id;

        return true;
    }

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
     * @return Util\BatchUpdater
     */
    public function createBatchUpdater()
    {
        return new Util\BatchUpdater($this->conn->getClient(), $this);
    }

    public function getInfo()
    {
        $this->conn->initialize();
        $json = $this->conn->getClient()->request("/{$this->name}/")->getContent();
        $info = $this->conn->getConfiguration()->getEncoder()->decode($json);
        return $info;
    }

    public function getName()
    {
        return $this->name;
    }
}
