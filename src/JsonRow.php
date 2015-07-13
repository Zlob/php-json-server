<?php

namespace JsonServer;

class JsonRow
{
    private $fields = [];

    public function __construct($data)
    {
        if (is_array($data)) {
            $this->fields = $data;
        } else {
            throw new \InvalidArgumentException("$data should be array");
        }
    }

    public function __get($key)
    {
        if (array_key_exists($key, $this->fields)) {
            return $this->fields[$key];
        } else {
            throw new \OutOfRangeException("there is no key $key in row");
        }
    }

    public function toArray()
    {
        return $this->fields;
    }

}