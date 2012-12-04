<?php
namespace CouchDB\Http;

use CouchDB\Authentication\AuthenticationInterface;

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
     *
     * @param  AuthenticationInterface $auth
     *
     * @return ClientInterface
     */
    public function connect(AuthenticationInterface $auth = null);

    /**
     * Check if the client is connected to the server
     *
     * @return boolean
     */
    public function isConnected();

    /**
     * Request
     *
     * @param string   $path
     * @param constant $method
     * @param string   $data
     * @param array    $headers
     *
     * @return ResponseInterface
     */
    public function request($path, $method = ClientInterface::METHOD_GET, $data = '', array $headers = array());
}
