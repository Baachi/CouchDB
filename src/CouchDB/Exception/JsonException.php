<?php

namespace CouchDB\Exception;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class JsonException extends \Exception
{
    public function __construct($data, $code = 0, $previous = null)
    {
        $message = sprintf('Json encode error [%s]: %s', self::$errors[json_last_error()], var_export($data, true));

        parent::__construct($message, $code, $previous);
    }
}
