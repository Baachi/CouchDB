<?php
namespace CouchDB\Encoder;

use CouchDB\Exception\JsonDecodeException;
use CouchDB\Exception\JsonEncodeException;

// JSON_ERROR_UTF8 is not available in php5.3.2
if (!defined('JSON_ERROR_UTF8')) {
    define('JSON_ERROR_UTF8', 8);
}

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class JSONEncoder implements EncoderInterface
{
    /**
     * Encode a value into json
     *
     * @param mixed $value
     * @throws JsonEncodeException
     * @return string
     */
    static public function encode($value)
    {
        $json = @json_encode($value);
        if ("null" === $json && $value !== null) {
            throw new JsonEncodeException($value);
        }
        return $json;
    }

    /**
     * Decode a json string
     *
     * @param string $json
     * @throws JsonDecodeException
     * @return mixed
     */
    static public function decode($json)
    {
        $value = json_decode($json, true);
        if (!$value) {
            throw new JsonDecodeException($json);
        }
        return $value;
    }

}
