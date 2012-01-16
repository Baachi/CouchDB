<?php
namespace CouchDB\Http;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class SocketClient extends AbstractClient
{
    protected $resource;

    public function __construct($host = '127.0.0.1', $port = 5984, $timeout = 1000)
    {
        if (!function_exists('fsockopen')) {
            throw new \RuntimeException('Function "fsockopen" must be available');
        }

        parent::__construct(array(
            'host' => $host,
            'port' => $port,
            'timeout' => $timeout,
        ));
    }

    /**
     * Connect to server
     */
    public function connect()
    {
        $this->resource = fsockopen($this->getOption('host'), $this->getOption('port'), $errno, $errstr, $this->getOption('timeout'));
        if (!$this->resource) {
            $this->resource = null;
            throw new \RuntimeException(sprintf('Unable to connect to %s (%s)', $this->getOption('host'), $errstr));
        }
    }

    /**
     * Check if the client is connected to the server
     *
     * @return boolean
     */
    public function isConnected()
    {
        return is_resource($this->resource);
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
        $request = $this->buildRequest($path, strtoupper($method), $headers, $data);

        if (!$this->isConnected()) {
            throw new \LogicException('Not connected to the server');
        }

        if (!fwrite($this->resource, $request)) {
            throw new \RuntimeException('Could not send request');
        }

        $headers   = array();
        $status    = '';
        $content   = '';

        $rawContent = array();
        while (false !== $line = fgets($this->resource)) {
            $rawContent[] = trim($line);
        }

        foreach ($rawContent as $line) {
            if (preg_match('@^HTTP/[\d\.]+\s*(\d+)\s*.*$@i', $line, $matches)) {
                $status = $matches[1];
            } else if (preg_match('@^([^\{\[]+):(.*)$@', $line, $matches)) {
                $headers[$matches[1]] = $matches[2];
            } else {
                $content .= $line;
            }
        }

        return new Response\Response($status, $content, $headers);
    }

    /**
     * Builds a HTTP request header
     * @param string $path
     * @param string $method
     * @param array $headers
     * @param string $data
     *
     * @return string
     */
    private function buildRequest($path, $method, array $headers, $data)
    {
        $string = "{$method} {$path} HTTP/1.1\n";

        $headers = array_merge(array(
            'Host' => $this->getOption('host')
        ), $headers);

        if ('' !== $data && null !== $data) {
            $headers['Content-Length'] = strlen($data);
        }

        foreach ($headers as $var => $value) {
            $string .= "{$var}: {$value}\n";
        }

        if ('' !== $data && null !== $data) {
            $string .= "\n{$data}";
        }

        return $string;
    }
}
