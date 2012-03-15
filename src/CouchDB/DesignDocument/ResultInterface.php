<?php
namespace CouchDB\DesignDocument;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
interface ResultInterface extends \IteratorAggregate, \Countable
{
    /**
     * Return all rows
     *
     * @return array
     */
    function getRows();

    /**
     * Return the first row
     *
     * @return false|array
     */
    function getFirstRow();

    /**
     * Return the last row
     *
     * @return false|array
     */
    function getLastRow();

    /**
     * Return the total amount of rows
     *
     * @return integer
     */
    function getTotalRows();

    /**
     * Gets the offset
     *
     * @return integer
     */
    function getOffset();

    /**
     * Return the array
     *
     * @return array
     */
    function toArray();
}
