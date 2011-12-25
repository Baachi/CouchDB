<?php
namespace CouchDB;

use CouchDB\Http\ClientInterface;
use CouchDB\Events\EventArgs;
use CouchDB\Query\QueryBuilder;
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
        $json = $this->conn->getClient()->request("/{$this->name}/{$id}")->getContent();
        $doc  = $this->conn->getConfiguration()->getEncoder()->decode($json);
        return $doc;
    }

    public function findAll()
    {
        $json = $this->conn->getClient()->request('/_all_docs')->getContent();
        $docs = $this->conn->getConfiguration()->getEncoder()->decode($json);

        return $docs;
    }

    public function info()
    {
        $json = $this->conn->getClient()->request("/{$this->name}/")->getContent();
        $info = $this->conn->getConfiguration()->getEncoder()->decode($json);
        return $info;
    }

    public function getName()
    {
        return $this->name;
    }
}
