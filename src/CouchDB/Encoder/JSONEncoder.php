<?php
namespace CouchDB\Encoder;

use CouchDB\Exception\JsonDecodeException;
use CouchDB\Exception\JsonEncodeException;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
final class JSONEncoder
{
    private function __construct() {}

    public static function encode($value)
    {
        $json = @json_encode($value);
        if ("null" === $json && $value !== null) {
            throw new JsonEncodeException($value);
        }
        if (false === $json) {
            throw new JsonEncodeException($value);
        }

        return $json;
    }

    public static function decode($json)
    {
        $value = json_decode($json, true);

        if (false === $value) {
            throw new JsonDecodeException($json);
        }

        return $value;
    }

}
