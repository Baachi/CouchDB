<?php
namespace CouchDB\Util;

use CouchDB\Http\ClientInterface;
use CouchDB\Encoder\JSONEncoder;
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
        $this->data['docs'][] = array('_id' => $id, '_rev' => $rev, '_deleted' => true);
        return $this;
    }

    public function execute()
    {
        $json    = JSONEncoder::encode($this->data);

        $response = $this->client->request(
            "/{$this->database->getName()}/_bulk_docs",
            ClientInterface::METHOD_POST,
            $json,
            array(
                'Content-Type' => 'application/json'
            )
        );

        return JSONEncoder::decode($response->getContent());
    }
}
