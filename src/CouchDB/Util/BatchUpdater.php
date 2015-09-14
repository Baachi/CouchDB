<?php

namespace CouchDB\Util;

use CouchDB\Database;
use CouchDB\Encoder\JSONEncoder;
use GuzzleHttp\ClientInterface;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class BatchUpdater
{
    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var Database
     */
    private $database;

    /**
     * @var array
     */
    private $data;

    /**
     * Constructor.
     *
     * @param \CouchDB\Http\ClientInterface $client
     * @param \CouchDB\Database             $db
     */
    public function __construct(ClientInterface $client, Database $db)
    {
        $this->client = $client;
        $this->database = $db;

        $this->data = ['docs' => []];
    }

    /**
     * Enqueue the document for a update.
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
     * Enqueue a document for deletion.
     *
     * @param string $id
     * @param string $rev
     *
     * @return BatchUpdater
     */
    public function delete($id, $rev)
    {
        $this->data['docs'][] = ['_id' => $id, '_rev' => $rev, '_deleted' => true];

        return $this;
    }

    /**
     * Execute the queue.
     *
     * @return mixed
     */
    public function execute()
    {
        $response = $this->client->request('POST', "/{$this->database->getName()}/_bulk_docs", [
            'body'    => JSONEncoder::encode($this->data),
            'headers' => ['Content-Type' => 'application/json'],
        ]);

        return JSONEncoder::decode((string) $response->getBody());
    }
}
