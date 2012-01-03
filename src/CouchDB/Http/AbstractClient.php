<?php
namespace CouchDB\Http;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
abstract class AbstractClient implements ClientInterface
{
    protected $options;

    public function __construct(array $options)
    {
        $this->options = $options;
    }

    public function getOption($key, $default = null)
    {
        return isset($this->options[$key]) ? $this->options[$key] : $default;
    }

    public function setOption($key, $value)
    {
        $this->options[$key] = $value;
    }
}
