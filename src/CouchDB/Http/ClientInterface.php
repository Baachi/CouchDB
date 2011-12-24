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
     * Request
     *
     * @param string $path
     * @param constant $method
     * @param string $data
     */
    function request($path, $method, $data);
}
