<?php

namespace CouchDB\Http;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class Request
{
    /**
     * @var string
     */
    private $path;

    /**
     * @var string
     */
    private $method;

    /**
     * @var string|null
     */
    private $data;

    /**
     * @var array
     */
    private $headers;

    /**
     * Constructor
     *
     * @param string      $path    The request path
     * @param string      $method  The request method (GET, PUT, POST, DELETE)
     * @param string|null $data    The request body
     * @param array       $headers Some additional headers
     */
    public function __construct($path, $method, $data, array $headers)
    {
        $this->path = $path;
        $this->method = $method;
        $this->data = $data;
        $this->headers = $headers;

        $this->addHeader('Content-Length', strlen($data));
    }

    /**
     * @param null|string $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @return null|string
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param array $headers
     */
    public function setHeaders($headers)
    {
        $this->headers = $headers;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Add a header value
     *
     * @param string $key
     * @param string $value
     */
    public function addHeader($key, $value)
    {
        $this->headers[$key] = $value;
    }

    /**
     * Returns a header key, or null.
     *
     * @param string $key
     *
     * @return string|null
     */
    public function getHeader($key)
    {
        if (isset($this->headers[$key])) {
            return $this->headers[$key];
        }

        return null;
    }

    /**
     * @param string $method
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param string $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }
}
