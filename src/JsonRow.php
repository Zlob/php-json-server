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

    //todo delete and action thrue table reference
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
    public function patch($data)
    {
        foreach($data as $field=>$value){
            if($this->$field === 'id'){
                continue;
            }
            $this->$field = $value;
        }
        //todo check not null
        $this->db->save();
    }

    /**
     *save changes into db file
     */
    public function delete()
    {
        $this->table->delete($this);
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