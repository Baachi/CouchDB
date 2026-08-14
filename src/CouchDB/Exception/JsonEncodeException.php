<?php

namespace Bachi\CouchDB\Exception;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class JsonEncodeException extends JsonException
{
    public function __construct($data, $code = 0, $previous = null)
    {
        $message = sprintf('Json encode error [%s]: %s', self::$errors[json_last_error()], var_export($data, true));

        parent::__construct($message, $code, $previous);
    }
}
