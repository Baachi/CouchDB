<?php

namespace CouchDB\DesignDocument;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
interface ResultInterface extends \IteratorAggregate, \Countable
{
    /**
     * Return all rows.
     *
     * @return array
     */
    public function getRows();

    /**
     * Return the first row.
     *
     * @return bool|array
     */
    public function getFirstRow();

    /**
     * Return the last row.
     *
     * @return bool|array
     */
    public function getLastRow();

    /**
     * Return the total amount of rows.
     *
     * @return int
     */
    public function getTotalRows();

    /**
     * Gets the offset.
     *
     * @return int
     */
    public function getOffset();

    /**
     * Return the array.
     *
     * @return array
     */
    public function toArray();
}
