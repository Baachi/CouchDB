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
     * Return the value from the given option.
     * If the option does not exist, it will return $default.
     *
     * @param string $name
     * @param mixed $default
     *
     * @return mixed
     */
    function getOption($name, $default = null);

    /**
     * Request
     *
     * @param string $path
     * @param constant $method
     * @param string $data
     *
     * @return \CouchDB\Http\Response\ResponseInterface
     */
    function request($path, $method = ClientInterface::METHOD_GET, $data = '');
}
