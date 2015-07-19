<?php

namespace JsonServer;

/**
 * Class JsonRow
 * @package JsonServer
 */
class JsonRow
{
    /**
     * The row fields
     *
     * @var array
     */
    private $fields = [];


    /**
     * Reference to the db class
     *
     * @var null
     */
    private $table;

    /**
     * Create new row instance
     *
     * @param $data - $row fields data
     * @param $table - instance of table, row belong to
     */
    public function __construct($data, &$table = null)
    {
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
     * Return row field value
     *
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
     * Set row field value
     *
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
     * Fields mass assignment
     *
     * @param $data
     */
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
     *
     * @return array
     */
    public function toArray()
    {
        return $this->fields;
    }

}