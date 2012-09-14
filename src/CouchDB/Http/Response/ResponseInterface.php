<?php
namespace CouchDB\Http\Response;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
interface ResponseInterface
{
    /**
     * Return the HTTP status code
     *
     * @return integer
     */
    public function getStatusCode();

    /**
     * Return the content
     *
     * @return string
     */
    public function getContent();

    /**
     * Return a header
     *
     * @param string $name
     */
    public function getHeader($name);

    /**
     * Return all headers
     */
    public function getHeaders();

    /**
     * Check if the response are successful.
     *
     * @return boolean
     */
    public function isSuccessful();

    /**
     * Return the content
     *
     * @return string
     */
    public function __toString();
}
