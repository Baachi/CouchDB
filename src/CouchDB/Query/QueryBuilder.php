<?php
namespace CouchDB\Query;

use CouchDB\Database;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class QueryBuilder
{
    protected $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function execute()
    {

    }
}
