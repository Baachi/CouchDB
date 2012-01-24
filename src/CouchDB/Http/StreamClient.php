<?php
namespace CouchDB\Http;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class StreamClient extends AbstractClient
{
    protected $connected = false;

    public function __construct($host = '127.0.0.1', $port = 5984, array $options = array())
    {
        parent::__construct(array(
            'host' => $host,
            'port' => $port
        ) + $options);
    }

    public function getDefaultOptions()
    {
        return array(
            'timeout' => 1000
        );
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
        $header = '';
        foreach ($headers as $key => $value) {
            $header .= sprintf("%s: %s\n", $key, $value);
        }

        $resource = @fopen(
            sprintf('http://%s:%d/%s', $this->getOption('host'), $this->getOption('port'), ltrim($path, '/')),
            'r',
            false,
            stream_context_create(array(
                'http' => array(
                    'method' => $method,
                    'content' => $data,
                    'ignore_errors' => true,
                    'max_redirects' => 0,
                    'user_agent'    => 'CouchDB Abstraction Layer',
                    'timeout'       => $this->getOption('timeout', 100),
                    'header'        => $header,
                )
            ))
        );

        if (!$resource) {
            throw new \RuntimeException(sprintf('Unable to open the connection to', $this->getOption('host')));
        }

        $body = '';
        while (!feof($resource)) {
            $body .= fgets($resource);
        }

        $metadata = stream_get_meta_data($resource);
        $rawHeaders = isset($metadata['wrapper_data']['headers']) ? $metadata['wrapper_data']['headers'] : $metadata['wrapper_data'];

        $headers = array();
        $status = 0;
        foreach ($rawHeaders as $header) {
            if (preg_match('@^HTTP/([\d\.]+)\s*(\d+).*$@i', $header, $matches)) {
                $status             = $matches[2];
                $headers['version'] = $matches[1];
            } else {
                list($key, $value)         = explode(':', $header, 2);
                $headers[strtolower($key)] = $value;
            }
        }

        return new Response\Response($status, $body, $headers);
    }

}
