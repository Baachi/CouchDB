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
    function log(array $message);
}
