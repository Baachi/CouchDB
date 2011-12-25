<?php
namespace CouchDB\Http;

use Buzz\Browser;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class BuzzClient implements ClientInterface
{
    protected $options;

    protected $browser;

    public function __construct(Browser $browser, $host = '127.0.0.1', $port = 5984)
    {
        $this->options = array(
            'host' => $host,
            'port' => $port,
        );

        $this->browser = $browser;
    }

    /**
     * Connect to server
     */
    public function connect()
    {
        return true;
    }

    /**
     * Check if the client is connected to the server
     *
     * @return boolean
     */
    public function isConnected()
    {
        return true;
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
     *
     * @return \CouchDB\Http\Response\ResponseInterface
     */
    public function request($path, $method = ClientInterface::METHOD_GET, $data = '')
    {
        $url = sprintf('http://%s:%d/%s', $this->getOption('host'), $this->getOption('port'), ltrim('/', $path));
        $response = $this->browser->call($url, $method, array(), $data);
        return new Response\Response($response->getStatusCode(), $response->getContent(), $response->getHeaders());
    }

}
