<?php

namespace CouchDB\Http;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class SocketClient extends AbstractClient
{
    /**
     * @var resource
     */
    protected $resource;

    public function __construct($host = '127.0.0.1', $port = 5984, $timeout = 1000)
    {
        if (!function_exists('fsockopen')) {
            throw new \RuntimeException('Function "fsockopen" must be available');
        }

        parent::__construct(array(
            'host'    => $host,
            'port'    => $port,
            'timeout' => $timeout,
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function getDefaultOptions()
    {
        return array(
            'keep-alive' => true,
        );
    }

    /**
     * {@inheritDoc}
     */
    public function connect()
    {
        $this->resource = fsockopen($this->getOption('host'), $this->getOption('port'), $errno, $errstr, $this->getOption('timeout'));

        if (!$this->resource) {
            $this->resource = null;
            throw new \RuntimeException(sprintf('Unable to connect to %s (%s)', $this->getOption('host'), $errstr));
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function isConnected()
    {
        return is_resource($this->resource);
    }

    /**
     * {@inheritDoc}
     */
    protected function doRequest(Request $request)
    {
        $request = $this->buildRequest($request);

        if (!$this->isConnected()) {
            $this->connect();
        }

        if (!fwrite($this->resource, $request)) {
            throw new \RuntimeException('Could not send request');
        }

        $rawHeader = array();
        while (strlen($line = trim(fgets($this->resource, 4096)))) {
            $rawHeader[] = $line;
        }

        if (empty($rawHeader)) {
            throw new \RuntimeException('Could not get response');
        }

        $headers = $this->readHeaders($rawHeader);

        $response = new Response\Response(
            $this->readHttpCode($rawHeader),
            $this->readContent($this->resource, $headers),
            $headers
        );

        if (!isset($headers['connection']) || 'keep-alive' !== $headers['connection']) {
            fclose($this->resource);
            $this->resource = null;
        }

        return $response;
    }

    /**
     * Builds a HTTP request header
     *
     * @param Request $request
     *
     * @return string
     */
    private function buildRequest(Request $request)
    {
        $string = "{$request->getMethod()} {$request->getPath()} HTTP/1.1\r\n";

        $request->addHeader('Host', $this->getOption('Host'));

        if (true === $this->getOption('keep-alive')) {
            $request->addHeader('Connection', 'Keep-Alive');
        } else {
            $request->addHeader('Connection', 'Close');
        }

        if ($this->getOption('username')) {
            $request->addHeader('Authorization', sprintf(
                'Basic %s', 
                base64_encode($this->getOption('username').':'.$this->getOption('password'))
            ));
        }

        foreach ($request->getHeaders() as $var => $value) {
            $string .= "{$var}: {$value}\r\n";
        }

        if ($request->getData()) {
            $string .= "\r\n{$request->getData()}";
        }

        return $string . "\n\n";
    }

    /**
     * Extract the HTTP status code
     *
     * @param array $rawHeader
     *
     * @return integer
     */
    private function readHttpCode(array $rawHeader)
    {
        list($line, ) = $rawHeader;
        preg_match('#^HTTP/1\.1\s+(\d+)\s+.*$#', $line, $matches);
        return (integer) $matches[1];
    }

    /**
     * Extract the response body
     *
     * @param resource $resource
     * @param array    $headers
     *
     * @return string
     */
    private function readContent($resource, array $headers)
    {
        $bytesToRead = 0;
        foreach ($headers as $key => $value) {
            if ('content-length' === $key) {
                $bytesToRead = (integer) $value;
            }
        }

        $content = '';

        do {
            $content .= $line = fgets($resource, $bytesToRead);
            $bytesToRead -= strlen($line);
        } while (($bytesToRead > 0) && ($line !== false));

        return $content;
    }

    /**
     * Extract all HTTP headers
     *
     * @param array $rawHeader
     *
     * @return array
     */
    private static function readHeaders(array $rawHeader)
    {
        $headers = array();
        foreach ($rawHeader as $line) {
            if (strpos($line, ':') !== false) {
                list($key, $value) = explode(':', $line, 2);
                $headers[strtolower($key)] = trim($value);
            }
        }

        return $headers;
    }

}
