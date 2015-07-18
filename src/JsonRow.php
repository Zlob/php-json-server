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
     * reference to a db class
     * @var null
     */
    private $db;

    /**
     * create new row instance
     * @param $data
     * @param null $db
     */
    public function __construct($data, &$db = null)
    {
        $this->db = &$db;
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
     * set row field value
     * @param $key
     * @return mixed
     */
    public function __set($key, $value)
    {
        if (array_key_exists($key, $this->fields)) {
            $this->fields[$key] = $value;
        } else {
            throw new \OutOfRangeException("there is no key $key in row");
        }
    }


    /**
     *save changes into db file
     */
    public function save()
    {
        //todo check not null
        $this->db->save();
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