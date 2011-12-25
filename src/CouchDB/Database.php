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

    public function find($id)
    {
        $this->conn->initialize();
        $json = $this->conn->getClient()->request("/{$this->name}/{$id}")->getContent();
        $doc  = $this->conn->getConfiguration()->getEncoder()->decode($json);
        return $doc;
    }

    public function findAll()
    {
        $this->conn->initialize();
        $json = $this->conn->getClient()->request("/{$this->name}/_all_docs")->getContent();
        $docs = $this->conn->getConfiguration()->getEncoder()->decode($json);

        return $docs;
    }

    public function insert($doc)
    {
        $this->conn->initialize();
        $json = $this->conn->getConfiguration()->getEncoder()->encode($doc);
        $response = $this->conn->getClient()->request("/{$this->name}", ClientInterface::METHOD_POST, $json, array('content-type' => 'application/json'));

        if (201 !== $response->getStatusCode()) {
            throw new \RuntimeException(sprintf('Unable to save %s: %s', var_export($doc, true), $response));
        }

        list($status, $id, $rev) = $this->conn->getConfiguration()->getEncoder()->decode($response->getContent());
        return array('id' => $id, 'rev' => $rev);
    }

    public function update($id, $doc)
    {

    }

    public function delete($id)
    {
        $this->conn->initialize();
        $response = $this->conn->getClient()->request("/{$this->name}/{$doc}", ClientInterface::METHOD_DELETE);

        if (200 !== $response->getStatusCode()) {
            throw new \RuntimeException(sprintf('Unable to delete %s', $id));
        }

        return true;
    }

    public function info()
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
