<?php
namespace CouchDB\DesignDocument;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class Result implements ResultInterface
{
    /**
     * @var array
     */
    protected $data;

    /**
     * Constructor
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Return the total amount of rows
     *
     * @return integer
     */
    public function getTotalRows()
    {
        return $this->data['total_rows'];
    }

    /**
     * Gets the offset
     *
     * @return integer
     */
    public function getOffset()
    {
        return $this->data['offset'];
    }

    /**
     * Return all rows
     *
     * @return array
     */
    public function getRows()
    {
        return $this->data['rows'];
    }

    /**
     * Return the first row
     *
     * @return bool|array
     */
    public function getFirstRow()
    {
        $rows = $this->data['rows'];
        if (empty($rows)) {
            return false;
        }

        $row = array_slice($rows, 0, 1);

        return current($row);
    }

    /**
     * Return the last row
     *
     * @return bool|array
     */
    public function getLastRow()
    {
        $rows = $this->data['rows'];
        if (empty($rows)) {
            return false;
        }

        $row = array_slice($rows, count($rows) - 1);

        return current($row);
    }

    /**
     * Return the array
     *
     * @return array
     */
    public function toArray()
    {
        return $this->data;
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->data['rows']);
    }

    /**
     * {@inheritDoc}
     */
    public function count()
    {
        return count($this->data['rows']);
    }
}
