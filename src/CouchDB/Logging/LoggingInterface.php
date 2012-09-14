<?php
namespace CouchDB\Logging;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
interface LoggingInterface
{
    /**
     * Logs a message
     *
     * @param array $message
     */
    public function log(array $message);
}
