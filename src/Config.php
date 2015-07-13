<?php

namespace JsonServer;

class Config implements \ArrayAccess
{
    /**
     * All of the configuration items.
     *
     * @var array
     */
    protected $items = [];

    /**
     * Create a new configuration repository.
     *
     * @param  array $items
     */
    public function __construct(array $items = [])
    {
        $this->items = json_decode(file_get_contents(__DIR__.'/../config/config.json'), true);
    }

    /**
     * Determine if the given configuration value exists.
     *
     * @param  string  $key
     * @return bool
     */
    public function has($key)
    {
        //todo
//        return array_has($this->items, $key);
    }

    /**
     * Get the specified configuration value.
     *
     * @param  string  $key
     * @return mixed
     */
    public function get($key)
    {
        return $this->items[$key];
    }

    /**
     * Set a given configuration value.
     *
     * @param  array|string  $key
     * @param  mixed   $value
     * @return void
     */
    public function set($key, $value = null)
    {
        $this->items[$key] = $value;
    }


    public function offsetExists($offset)
    {
        return array_key_exists($this->items, $offset);
    }

    public function offsetGet($offset)
    {
        return $this->items[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $this->items[] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->items[$offset]);
    }
}
