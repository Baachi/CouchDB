<?php
namespace CouchDB\Http;

use Guzzle\Service\Client;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class GuzzleClient implements ClientInterface
{
    protected $client;

    protected $connected = false;

    public function __construct($host = '127.0.0.1', $port = 5984)
    {
        $this->client = new Client(sprintf('https://%s:%d', $host, $port));
    }

    /**
     * Connect to server
     */
    public function connect()
    {
        $this->connected = true;
    }

    /**
     * Check if the client is connected to the server
     *
     * @return boolean
     */
    public function isConnected()
    {
        return $this->connected;
    }

    /**
     * Return the value from the given option.
     * If the option does not exist, it will return $default.
     *
     * @param string $name
     * @param mixed $default
     *
     * @return mixed
     */
    public function getOption($name, $default = null)
    {
        return isset($this->options[$name]) ? $this->options[$name] : $default;
    }

    /**
     * Request
     *
     * @param string $path
     * @param constant $method
     * @param string $data
     * @param array $headers
     *
     * @return \CouchDB\Http\Response\ResponseInterface
     */
    public function request($path, $method = ClientInterface::METHOD_GET, $data = '', array $headers = array())
    {
        $response = $this->client->createRequest($method, $path, $headers, $data)->send();
        return new Response\Response($response->getStatusCode(), $response->getBody(), $response->getHeaders());
    }
}
