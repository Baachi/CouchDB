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
    public function getRows();

    /**
     * Return the first row
     *
     * @return false|array
     */
    public function getFirstRow();

    /**
     * Return the last row
     *
     * @return false|array
     */
    public function getLastRow();

    /**
     * Return the total amount of rows
     *
     * @return integer
     */
    public function getTotalRows();

    /**
     * Gets the offset
     *
     * @return integer
     */
    public function getOffset();

    /**
     * Return the array
     *
     * @return array
     */
    public function toArray();
}
