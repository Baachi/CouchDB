<?php
namespace CouchDB\Encoder;

use CouchDB\Exception\JsonDecodeException;
use CouchDB\Exception\JsonEncodeException;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class NativeEncoder implements EncoderInterface
{
    /**
     * Encode a value into json
     *
     * @param mixed $value
     * @throws JsonEncodeException
     * @return string
     */
    public function encode($value)
    {
        $json = json_encode($value);
        if (!$json) {
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
    public function decode($json)
    {
        $value = json_decode($json, true);
        if (!$value) {
            throw new JsonDecodeException($json);
        }
        return $value;
    }

}
