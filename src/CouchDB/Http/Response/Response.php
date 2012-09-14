<?php
namespace CouchDB\Http\Response;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class Response implements ResponseInterface
{
    /**
     * @var integer
     */
    protected $statusCode;

    /**
     * @var string
     */
    protected $content;

    /**
     * @var array
     */
    protected $headers;

    /**
     * Constructor
     *
     * @param integer $status
     * @param string $content
     * @param array $headers
     */
    public function __construct($status, $content, array $headers)
    {
        $this->statusCode = (integer) $status;
        $this->content    = (string) $content;
        $this->headers    = $headers;
    }

    /**
     * Return the HTTP status code
     *
     * @return integer
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Return the content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Return a header
     *
     * @param string $name
     */
    public function getHeader($name)
    {
        if (!isset($this->headers[$name])) {
            return null;
        }

        return $this->headers[$name];
    }

    /**
     * Return true if the http header exist, false otherwise.
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasHeader($name)
    {
        return isset($this->headers[$name]);
    }

    /**
     * Return all headers
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Check if the response are successful.
     *
     * @return boolean
     */
    public function isSuccessful()
    {
        return ($this->statusCode >= 200) && ($this->statusCode < 300);
    }

    /**
     * Return the content
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getContent();
    }
}
