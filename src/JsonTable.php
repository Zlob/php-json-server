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
     * Table rows
     *
     * @var array
     */
    private $rows = [];

    /**
     * Reference to the db object
     *
     * @var null
     */
    private $db;

    /**
     * Name of associated table
     *
     * @var
     */
    private $tabName;

    /**
     * Create a new JsonTable instance
     *
     * @param $data
     */
    public function __construct($data, $tabName, &$db = null)
    {
        $this->db = &$db;
        $this->tabName = $tabName;
        if (is_array($data)) {
            foreach ($data as $row) {
                if (is_array($row)) {
                    $this->rows[] = new JsonRow($row, $this);
                } elseif (get_class($row) === 'JsonServer\JsonRow') {
                    $this->rows[] = &$row;
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
     *
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
     * Return row with id $id
     *
     * @param $id
     * @return null
     */
    public function find($id)
    {
        $result = $this->filter(function ($item) use ($id) {
            return $item->id == $id;
        });
        if (count($result->rows) > 0) {
            return $result[0];
        } else {
            return null;
        }
    }

    /**
     * Filter rows by related entity id
     *
     * @param $parent - array with 'table' and 'id' keys
     * @return $this|JsonTable
     */
    public function filterByParent($parent)
    {
        if ($parent) {
            return $this->where($this->getParentKeyName($parent['table']), $parent['id']);
        }
        return $this;
    }

    /**
     * Add new resource
     *
     * @param $data
     * @return int
     */
    public function insert($data)
    {
        $row = new JsonRow($data, $this);
        $this->rows[] = $row;
        return $row;
    }

    /**
     * Update resource
     *
     * @param $id
     * @param $data
     * @return int
     */
    public function update($id, $data)
    {
        $row = $this->find($id);
        if(!$row){
            throw new \OutOfRangeException("there is no resource with id $id");
        }
        $row->setData($data);
        return $row;
    }

    /**
     * Delete resource
     *
     * @param $id
     * @return int
     */
    public function delete($id)
    {
        foreach ($this->rows as $key => $row) {
            if ($row->id == $id) {
                unset($this->rows[$key]);
                return 0;
            }
        }
        throw new \OutOfRangeException("there is no resource with id $id");
    }

    /**
     * Return free id number
     *
     * @return mixed
     */
    public function getNewId()
    {
        $ids = [];
        foreach ($this->rows as $row) {
            $ids[] = $row->id;
        }
        if(count($ids) === 0){
            return 1;
        }
        sort($ids);
        $max = end($ids);
        return ++$max;
    }

    /**
     * Return array representation of rows
     *
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
     * Return count of rows in table
     * @return int
     */
    public function count()
    {
        return count($this->rows);
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

    /**
     * Filter rows with callback function
     *
     * @param $callback
     * @return static
     */
    private function filter($callback)
    {
        return new static(array_filter($this->rows, $callback), $this->tabName);
    }

    /**
     * Return parent relation field in right form
     *
     * @param $noun
     * @return string
     */
    private function getParentKeyName($noun)
    {
        if (!(Config::get('urlNamingForm') === Config::get('relationsNamingForm'))) {
            $method = Config::get('relationsNamingForm');
            return Inflector\Inflector::$method($noun) . "_id";
        } else {
            return $noun . "_id";
        }
    }

}