<?php


namespace JsonServer;

class Config implements \ArrayAccess
{
    /**
     * All of the configuration items.
     * @var array
     */
    protected $items = [];


    /**
     * singleton instance
     * @var null
     */
    private static $instance = null;

    /**
     * Create a new configuration repository.
     * @param  array $items
     */
    private function __construct(array $items = [])
    {
        $this->items = json_decode(file_get_contents(__DIR__.'/../config/config.json'), true);
    }

    /**
     *
     */
    protected function __clone() {
    }

    /**
     * return configuration instance
     * @return Config
     */
    static public function getInstance() {
        if(is_null(self::$instance))
        {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Determine if the given configuration value exists.
     *
     * @param  string  $key
     * @return bool
     */
    public function has($key)
    {
        return array_key_exists($key, $this->items);
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