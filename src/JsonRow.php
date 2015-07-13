<?php

namespace JsonServer;

/**
 * Class JsonRow
 * @package JsonServer
 */
class JsonRow
{
    /**
     * array of the row fields
     * @var array
     */
    private $fields = [];

    /**
     * create new row instance
     * @param $data
     */
    public function __construct($data)
    {
        if (is_array($data)) {
            $this->fields = $data;
        } else {
            throw new \InvalidArgumentException("$data should be array");
        }
    }

    /**
     * return row field value
     * @param $key
     * @return mixed
     */
    public function __get($key)
    {
        if (array_key_exists($key, $this->fields)) {
            return $this->fields[$key];
        } else {
            throw new \OutOfRangeException("there is no key $key in row");
        }
    }

    /**
     * Return array representation of row
     * @return array
     */
    public function toArray()
    {
        return $this->fields;
    }

}