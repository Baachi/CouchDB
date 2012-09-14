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
    /**
     * @var \CouchDB\Http\ClientInterface
     */
    private $client;

    /**
     * @var \CouchDB\Database
     */
    private $database;

    /**
     * @var array
     */
    private $data;

    /**
     * Constructor
     *
     * @param \CouchDB\Http\ClientInterface $client
     * @param \CouchDB\Database $db
     */
    public function __construct(ClientInterface $client, Database $db)
    {
        $this->client = $client;
        $this->database = $db;

        $this->data = array('docs' => array());
    }

    /**
     * Enqueue the document for a update
     *
     * @param array $doc
     *
     * @return BatchUpdater
     */
    public function update(array $doc)
    {
        $this->data['docs'][] = $doc;

        return $this;
    }

    /**
     * Enqueue a document for deletion
     *
     * @param string $id
     * @param string $rev
     *
     * @return BatchUpdater
     */
    public function delete($id, $rev)
    {
        $this->data['docs'][] = array('_id' => $id, '_rev' => $rev, '_deleted' => true);

        return $this;
    }

    /**
     * Execute the queue
     *
     * @return mixed
     */
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
