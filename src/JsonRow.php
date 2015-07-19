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

    //todo delete and action true table reference
    /**
     * reference to a db class
     * @var null
     */
    private $db;

    /**
     * reference to the db class
     * @var null
     */
    private $table;

    /**
     * create new row instance
     * @param $data
     * @param null $db
     */
    public function __construct($data, &$db = null, &$table = null)
    {
        $this->db = &$db;
        $this->table = &$table;
        if (is_array($data)) {
            $this->fields = $data;
        } else {
            throw new \InvalidArgumentException("$data should be array");
        }
        if (!array_key_exists('id', $this->fields) || !is_numeric($this->fields['id'])) {
            $this->fields['id'] = $this->table->getNewId();
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


    public function setData($data)
    {
        foreach ($data as $field => $value) {
            if ($field === 'id') {
                continue;
            }
            $this->$field = $value;
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