<?php
namespace CouchDB\Encoder;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
interface EncoderInterface
{
    /**
     * Encode a value into json
     *
     * @param  mixed               $value
     * @throws JsonEncodeException
     * @return string
     */
    public static function encode($value);

    /**
     * Decode a json string
     *
     * @param  string              $json
     * @throws JsonDecodeException
     * @return mixed
     */
    public static function decode($json);
}
