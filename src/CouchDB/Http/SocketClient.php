<?php
namespace CouchDB\Http;

use CouchDB\Auth;

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

    public function getDefaultOptions()
    {
        return array(
            'keep-alive' => true,
        );
    }

    /**
     * Connect to server
     */
    public function connect(Auth\AuthInterface $auth = null)
    {
        $this->resource = fsockopen($this->getOption('host'), $this->getOption('port'), $errno, $errstr, $this->getOption('timeout'));
        if (!$this->resource) {
            $this->resource = null;
            throw new \RuntimeException(sprintf('Unable to connect to %s (%s)', $this->getOption('host'), $errstr));
        }

        if ($auth) {
            $this->authAdapter = $auth;
            $this->authAdapter->authorize($this);
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
        if ($this->authAdapter) {
            $headers = array_merge($headers, $this->authAdapter->getHeaders());
        }

        $request = $this->buildRequest($path, strtoupper($method), $headers, $data);

        if (!$this->isConnected()) {
            throw new \LogicException('Not connected to the server');
        }

        if (!fwrite($this->resource, $request)) {
            throw new \RuntimeException('Could not send request');
        }

        $rawHeader = '';
        while (strlen($line = trim(fgets($this->resource, 4096)))) {
            $rawHeader .= $line . "\n";
        }

        return new Response\Response(
            self::readHttpCode($rawHeader),
            self::readContent($this->resource, $rawHeader),
            self::readHeaders($rawHeader)
        );
    }

    public function setTestConnection($resource)
    {
        $this->resource = $resource;
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

        if (true === $this->getOption('keep-alive')) {
            $headers['connection'] = 'keep-alive';
        } else {
            $headers['connection'] = 'close';
        }

        foreach ($headers as $var => $value) {
            $string .= "{$var}: {$value}\n";
        }

        if ('' !== $data && null !== $data) {
            $string .= "\n{$data}";
        }

        return $string . "\n\n";
    }

    private static function readHttpCode($rawHeader)
    {
        return preg_match('@^HTTP/[\d\.]+ (\d+)@i', $rawHeader, $regs) ? $regs[1] : null;
    }

    private static function readContent($resource, $rawHeader)
    {
        $bytesToRead = preg_match('@content\-length: (\d+)@i', $rawHeader, $regs) ? $regs[1] : 0;

        $content = '';
        do {
            $content .= $line = fgets($resource, $bytesToRead);
            $bytesToRead -= strlen($line);
        }
        while (($bytesToRead > 0) && ($line !== false));

        return $content;
    }

    private static function readHeaders($rawHeader)
    {
        $headers = array();
        foreach (explode("\n", $rawHeader) as $line) {
            if (strpos($line, ':') !== false) {
                list($key, $value) = explode(':', $line, 2);
                $headers[strtolower($key)] = trim($value);
            }
        }
        return $headers;
    }

}
