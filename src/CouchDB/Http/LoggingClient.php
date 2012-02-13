<?php
namespace CouchDB\Http;

use CouchDB\Logging\LoggingInterface;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class LoggingClient implements ClientInterface
{
    /**
     * @var LoggingInterface
     */
    protected $logger;

    /**
     * @var ClientInterface
     */
    protected $client;

    /**
     * @var integer
     */
    protected $totalDuration;

    public function __construct(LoggingInterface $logger, ClientInterface $client)
    {
        $this->client = $client;
        $this->logger = $logger;
        $this->totalDuration = 0;
    }

    /**
     * Connect to server
     */
    public function connect()
    {
        return $this->client->connect();
    }

    /**
     * Check if the client is connected to the server
     *
     * @return boolean
     */
    public function isConnected()
    {
        return $this->client->isConnected();
    }

    /**
     * Request
     *
     * @param string $path
     * @param string $method
     * @param string $data
     * @param array $headers
     *
     * @return \CouchDB\Http\Response\ResponseInterface
     */
    public function request($path, $method = ClientInterface::METHOD_GET, $data = '', array $headers = array())
    {
        $start = microtime(true);
        $response = $this->client->request($path, $method, $data, $headers);
        $duration = $start - microtime(true);

        $this->logger->log(array(
            'duration'         => $duration,

            'request_method'   => $method,
            'request_path'     => $path,
            'request_data'     => $data,
            'request_headers'  => $headers,

            'response_headers' => $response->getHeaders(),
            'response_status'  => $response->getStatusCode(),
            'response_body'    => $response->getContent()
        ));

        $this->totalDuration += $duration;

        return $response;
    }

    /**
     * Get the logger
     *
     * @return LoggingInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * Sets the new logger
     *
     * @param LoggingInterface $logger
     */
    public function setLogger(LoggingInterface $logger)
    {
        $this->logger = $logger;
    }
}
