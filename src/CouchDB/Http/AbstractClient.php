<?php
namespace CouchDB\Http;

use CouchDB\Auth;
/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
abstract class AbstractClient implements ClientInterface
{
    protected $options;

    /**
     * @var Auth\AuthInterface
     */
    protected $authAdapter;

    public function __construct(array $options)
    {
        $this->options = array_merge($this->getDefaultOptions(), $options);
    }

    public function getOption($key, $default = null)
    {
        return isset($this->options[$key]) ? $this->options[$key] : $default;
    }

    public function setOption($key, $value)
    {
        $this->options[$key] = $value;
    }

    public function getDefaultOptions()
    {
        return array();
    }
}
