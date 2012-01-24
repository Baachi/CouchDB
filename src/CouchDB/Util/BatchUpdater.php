<?php
namespace CouchDB\Util;

use CouchDB\Http\ClientInterface;
use CouchDB\Database;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class BatchUpdater
{
    private $client;

    private $database;

    private $data;

    public function __construct(ClientInterface $client, Database $db)
    {
        $this->client = $client;
        $this->database = $db;

        $this->data = array('docs' => array());
    }

    public function update($doc)
    {
        $this->data['docs'][] = $doc;
        return $this;
    }

    public function delete($id, $rev)
    {
        $this->data['docs'] = array('_id' => $id, '_rev' => $rev, '_deleted' => true);
        return $this;
    }

    public function execute()
    {
        $encoder =$this->database->getConnection()->getConfiguration()->getEncoder();
        $json = $encoder->encode($this->data);
        $response = $this->client->request("/{$this->databse->getName()}/_bulk_docs", ClientInterface::METHOD_POST, $json);

        return $encoder->decode($response->getContent());
    }
}
