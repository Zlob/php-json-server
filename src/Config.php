<?php


namespace JsonServer;

class Config implements \ArrayAccess
{
    /**
     * All of the configuration items.
     *
     * @var array
     */
    protected static $items = [];

    /**
     * Singleton instance
     *
     * @var null
     */
    private static $instance = null;

    /**
     * Create a new configuration repository.
     */
    private function __construct()
    {
        self::$items = include_once(__DIR__ . '/../config/config.php');
    }

    protected function __clone()
    {
    }

    /**
     * Return configuration instance
     * @return Config
     */
    static public function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Determine if the given configuration value exists.
     *
     * @param  string $key
     * @return bool
     */
    public static function has($key)
    {
        if (self::$instance === null) {
            self::getInstance();
        }
        return array_key_exists($key, self::$items);
    }

    /**
     * Get the specified configuration value.
     *
     * @param  string $key
     * @return mixed
     */
    public static function get($key)
    {
        if (self::$instance === null) {
            self::getInstance();
        }
        return self::$items[$key];
    }

    /**
     * Set a given configuration value.
     *
     * @param  array|string $key
     * @param  mixed $value
     * @return void
     */
    public static function set($key, $value = null)
    {
        if (self::$instance === null) {
            self::getInstance();
        }
        self::$items[$key] = $value;
    }


    public function offsetExists($offset)
    {
        return array_key_exists(self::$items, $offset);
    }

    public function offsetGet($offset)
    {
        return self::$items[$offset];
    }

    public function offsetSet($offset, $value)
    {
        self::$items[] = $value;
    }

    public function offsetUnset($offset)
    {
        unset(self::$items[$offset]);
    }

}