<?php

namespace CouchDB\Exception;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class JsonDecodeException extends JsonException
{
    public function __construct($json, $code = 0, $previous = null)
    {
        $message = sprintf('Json decode error [%s]: %s', self::$errors[json_last_error()], $json);

        parent::__construct($message, $code, $previous);
    }
}
