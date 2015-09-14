<?php

namespace CouchDB\Encoder;

use CouchDB\Exception\JsonDecodeException;
use CouchDB\Exception\JsonEncodeException;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
final class JSONEncoder
{
    private function __construct()
    {
    }

    public static function encode($value)
    {
        set_error_handler(function () use ($value) {
            throw new JsonEncodeException($value);
        });

        $json = json_encode($value);

        if ('null' === $json && $value !== null) {
            throw new JsonEncodeException($value);
        }

        restore_error_handler();

        return $json;
    }

    public static function decode($json)
    {
        set_error_handler(function () use ($json) {
            throw new JsonDecodeException($json);
        });

        $value = json_decode($json, true);

        if (false === $value) {
            throw new JsonDecodeException($json);
        }

        restore_error_handler();

        return $value;
    }
}
