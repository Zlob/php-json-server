<?php

namespace JsonServer;

use Doctrine\Common\Inflector;

/**
 * Class JsonTable
 * @package JsonServer
 */
class JsonTable implements \ArrayAccess
{
    /**
     * @var array
     * Array of table rows
     */
    private $rows = [];

    /**
     * create a new JsonTable instance
     * @param $data
     */
    public function __construct($data)
    {
        if (is_array($data)) {
            foreach ($data as $row) {
                if (is_array($row)) {
                    $this->rows[] = new JsonRow($row);
                } elseif (get_class($row) === 'JsonServer\JsonRow') {
                    $this->rows[] = $row;
                } else {
                    throw new \InvalidArgumentException('data should be array or object of JsonRow');
                }
            }
        } else {
            throw new \InvalidArgumentException('data should be array or object of JsonRow');
        }

    }

    /**
     * Filter table rows by $key with $value
     * @param $key
     * @param $value
     * @return JsonTable
     */
    public function where($key, $value)
    {
        return $this->filter(function ($item) use ($key, $value) {
            return $item->$key == $value;
        });
    }

    /**
     * Return row whth id $id
     * @param $id
     * @return null
     */
    public function find($id)
    {
        $result = $this->filter(function ($item) use ($id) {
            return $item->id == $id;
        });
        $va = count($result->rows);
        if ($va > 0) {
            return $result[0];
        } else {
            return null;
        }
    }

    /**
     * Filter rows by related entity id
     * @param $parentName
     * @param $parentId
     * @return $this|JsonTable
     */
    public function filterByParent($parentName, $parentId)
    {
        if ($parentName != null) {
            return $this->where($this->getParentKeyName($parentName), $parentId);
        }
        return $this;
    }

    /**
     * Filter rows with callback function
     * @param $callback
     * @return static
     */
    private function filter($callback)
    {
        return new static(array_filter($this->rows, $callback));
    }

    /**
     * return parant relation field in right form
     * @param $parentName
     * @return string
     */
    private function getParentKeyName($noun)
    {
        if (!(Config::get('urlNamingForm') === Config::get('relationsNamingForm'))){
            //todo another method
            $method = Config::get('relationsNamingForm').'ize';
            return Inflector\Inflector::$method($noun) . "_id";
        }
        else{
            return $noun . "_id";
        }
    }

    /**
     * Return array representation of rows
     * @return array
     */
    public function toArray()
    {
        $result = [];
        foreach ($this->rows as $row) {
            $result[] = $row->toArray();
        }
        return $result;
    }

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return array_key_exists($this->rows, $offset);
    }

    /**
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->rows[$offset];
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        $this->rows[] = $value;
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->rows[$offset]);
    }
}