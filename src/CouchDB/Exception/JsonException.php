<?php

namespace CouchDB\Exception;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class JsonException extends \Exception
{
    protected static $errors = [
        JSON_ERROR_NONE           => 'unknown error',
        JSON_ERROR_DEPTH          => 'The maximum stack depth has been exceeded',
        JSON_ERROR_SYNTAX         => 'Syntax error',
        JSON_ERROR_CTRL_CHAR      => 'Control character error',
        JSON_ERROR_STATE_MISMATCH => 'Invalid or malformed JSON',
        JSON_ERROR_UTF8           => 'Malformed UTF-8 characters',
    ];
}
