<?php
namespace MODXSlim\Api;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;

class Collection implements ArrayAccess, Countable, IteratorAggregate
{
    /**
     * The source data
     *
     * @var array
     */
    protected $data = [];
    /**
     * @param array $items Pre-populate collection with this key-value array
     */
    public function __construct(array $items = [])
    {
        $this->replace($items);
    }
    /**
     * Set collection item
     *
     * @param mixed $key   The data key
     * @param mixed $value The data value
     */
    public function set($key, $value)
    {
        $this->data[$key] = $value;
    }
    /**
     * Get collection item for key
     *
     * @param mixed $key     The data key
     * @param mixed $default The default value to return if data key does not exist
     *
     * @return mixed The key's value, or the default value
     */
    public function get($key, $default = null)
    {
        return $this->has($key) ? $this->data[$key] : $default;
    }
    /**
     * Add item to collection, replacing existing items with the same data key
     *
     * @param array $items Key-value array of data to append to this collection
     */
    public function replace(array $items)
    {
        foreach ($items as $key => $value) {
            $this->set($key, $value);
        }
    }
    /**
     * Get all items in collection
     *
     * @return array The collection's source data
     */
    public function all()
    {
        return $this->data;
    }
    /**
     * Get collection keys
     *
     * @return array The collection's source data keys
     */
    public function keys()
    {
        return array_keys($this->data);
    }
    /**
     * Does this collection have a given key?
     *
     * @param mixed $key The data key
     *
     * @return bool
     */
    public function has($key)
    {
        return array_key_exists($key, $this->data);
    }
    /**
     * Remove item from collection
     *
     * @param mixed $key The data key
     */
    public function remove($key)
    {
        unset($this->data[$key]);
    }
    /**
     * Remove all items from collection
     */
    public function clear()
    {
        $this->data = [];
    }
    /**
     * Does this collection have a given key?
     *
     * @param  mixed $key The data key
     *
     * @return bool
     */
    public function offsetExists($key)
    {
        return $this->has($key);
    }
    /**
     * Get collection item for key
     *
     * @param mixed $key The data key
     *
     * @return mixed The key's value, or the default value
     */
    public function offsetGet($key)
    {
        return $this->get($key);
    }
    /**
     * Set collection item
     *
     * @param mixed $key   The data key
     * @param mixed $value The data value
     */
    public function offsetSet($key, $value)
    {
        $this->set($key, $value);
    }
    /**
     * Remove item from collection
     *
     * @param mixed $key The data key
     */
    public function offsetUnset($key)
    {
        $this->remove($key);
    }
    /**
     * Get number of items in collection
     *
     * @return int
     */
    public function count()
    {
        return count($this->data);
    }
    /**
     * Get collection iterator
     *
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->data);
    }
}
