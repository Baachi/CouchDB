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
    function getStatusCode();

    /**
     * Return the content
     *
     * @return string
     */
    function getContent();

    /**
     * Return a header
     *
     * @param string $name
     */
    function getHeader($name);

    /**
     * Return all headers
     */
    function getHeaders();

    /**
     * Check if the response are successful.
     *
     * @return boolean
     */
    function isSuccessful();

    /**
     * Return the content
     *
     * @return string
     */
    function __toString();
}
