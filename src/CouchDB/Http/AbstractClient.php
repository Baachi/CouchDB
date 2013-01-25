<?php

namespace CouchDB\Http;

use Doctrine\Common\EventManager;
use CouchDB\Events\EventArgs;
use CouchDB\Events;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
abstract class AbstractClient implements ClientInterface
{
    /**
     * @var array
     */
    protected $options;

    /**
     * Constructor
     *
     * @param array $options
     */
    public function __construct(array $options)
    {
        $this->options = array_merge($this->getDefaultOptions(), $options);
    }

    /**
     * Get a option. If the option was not found,
     * $default will be returned.
     *
     * @param string $key     The option key
     * @param mixed  $default The default value
     *
     * @return mixed
     */
    public function getOption($key, $default = null)
    {
        return isset($this->options[$key]) ? $this->options[$key] : $default;
    }

    /**
     * Set a option
     *
     * @param string $key   The option key
     * @param mixed  $value The option value
     */
    public function setOption($key, $value)
    {
        $this->options[$key] = $value;
    }

    /**
     * Return some default options.
     * These option will be merged with the user options.
     *
     * @return array
     */
    public function getDefaultOptions()
    {
        return array();
    }

    /**
     * {@inheritDoc}
     */
    public function request($path, $method = ClientInterface::METHOD_GET, $data = '', array $headers = array())
    {
        $request = new Request($path, $method, $data, $headers);

        return $this->doRequest($request);
    }


    /**
     * Perform the request to the couchdb server.
     *
     * @param Request $request The request object
     *
     * @return ResponseInterface
     */
    abstract protected function doRequest(Request $request);
}
