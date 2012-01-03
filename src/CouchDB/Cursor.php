<?php
namespace CouchDB;

use Iterator, Countable, Closure;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class Cursor implements Iterator, Countable
{
    /**
     * @var array
     */
    private $documents;

    /**
     * @param array $documents
     */
    public function __construct(array $documents)
    {
       $this->documents = $documents;
    }

    /**
     * Filter the documents with the given function.
     * It will return a new instance of the cursor object.
     *
     * @param  Closure $closure
     * @return Cursor
     */
    public function filter(Closure $closure)
    {
        $documents = array();
        foreach ($this as $document) {
            if (true === $closure($document)) {
                $documents[] = $document;
            }
        }

        return new static($documents);
    }

    /**
     * Map the cursor with the given function.
     * It return a new instance from the cursor
     *
     * @param Closure $closure
     * @return Cursor
     */
    public function map(Closure $closure)
    {
        $documents = array();
        foreach ($this as $document) {
            $documents[] = $closure($document);
        }

        return new static($documents);
    }

    /**
     * Sort the cursor object with the given function.
     * It will return a new instance.
     *
     * @param Closure $closure
     * @return Cursor
     */
    public function sort(Closure $closure)
    {
        $documents = $this->documents;
        uasort($documents, $closure);
        return new static($documents);
    }

    /**
     * Returns the first document by success, false otherwise.
     *
     * @return mixed
     */
    public function first()
    {
        $copy = $this->documents;
        return reset($copy);
    }

    /**
     * Return the last document by success, false otherwise.
     *
     * @return mixed
     */
    public function last()
    {
        $copy = $this->documents;
        return end($copy);
    }

    /**
     * Get all fields
     *
     * @return array
     */
    public function fields()
    {
        return array_keys($this->current());
    }

    /**
     * Returns the current document
     *
     * @return mixed
     */
    public function current()
    {
        return current($this->documents);
    }

    /**
     * Return the next document
     *
     * @return mixed
     */
    public function next()
    {
        return next($this->documents);
    }

    /**
     * Return the previous document
     *
     * @return mixed
     */
    public function previous()
    {
        return prev($this->documents);
    }

    /**
     * Returns the key of the current document
     *
     * @return mixed
     */
    public function key()
    {
        return key($this->documents);
    }

    /**
     * Return true if the iterator valid, false otherwise.
     *
     * @return bool
     */
    public function valid()
    {
        return false !== current($this->documents);
    }

    /**
     * Return the first document by success, false otherwise.
     *
     * @return mixed
     */
    public function rewind()
    {
        return reset($this->documents);
    }

    /**
     * Return the amount of the documents
     *
     * @return int
     */
    public function count()
    {
        return count($this->documents);
    }

    /**
     * Convert the cursor object to an array.
     *
     * @return array
     */
    public function toArray()
    {
        return iterator_to_array($this);
    }
}
