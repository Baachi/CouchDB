<?php
namespace CouchDB\Http;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
interface ClientInterface
{
    const METHOD_POST   = 'POST';
    const METHOD_GET    = 'GET';
    const METHOD_PUT    = 'PUT';
    const METHOD_DELETE = 'DELETE';
    const METHOD_COPY   = 'COPY';
    const METHOD_HEAD   = 'HEAD';

    /**
     * Connect to server
     */
    function connect();

    /**
     * Check if the client is connected to the server
     *
     * @return boolean
     */
    function isConnected();

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
    function request($path, $method = ClientInterface::METHOD_GET, $data = '', array $headers = array());
}
