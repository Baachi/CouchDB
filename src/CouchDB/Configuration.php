<?php
namespace CouchDB;

use CouchDB\Encoder\EncoderInterface;
use CouchDB\Encoder\NativeEncoder;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class Configuration
{
    /**
     * @var EncoderInterface
     */
    private $encoder;

    public function __construct(EncoderInterface $encoder = null)
    {
        $this->encoder = $encoder ?: new NativeEncoder();
    }

    /**
     * Set encoder
     *
     * @param EncoderInterface $encoder
     */
    public function setEncoder(EncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    /**
     * Get encoder
     *
     * @return EncoderInterface
     */
    public function getEncoder()
    {
        return $this->encoder;
    }
}
