<?php

namespace JsonServer;

use Doctrine\Common\Inflector;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class JsonTable
 * @package JsonServer
 */
class Table implements \ArrayAccess
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
     * sorting field
     * @var string
     */
    private $sortField = 'id';

    /**
     * sorting order
     * @var string
     */
    private $sortOrder = 'asc';

    /**
     * start position
     * @var null
     */
    private $start;

    /**
     *  end position
     * @var null
     */
    private $end;

    /**
     *  result limit
     * @var null
     */
    private $limit;

    /**
     *  embed resources
     * @var array
     */
    private $embeds;

    /**
     * Create a new JsonTable instance
     *
     * @param $data
     * @throws \InvalidArgumentException
     */
    public function __construct($data, $tabName, &$db = null)
    {
        $this->db = &$db;
        $this->tabName = $tabName;
        if (is_array($data)) {
            foreach ($data as $row) {
                if (is_array($row)) {
                    $this->rows[] = new Row($row, $this);
                } elseif (get_class($row) === 'JsonServer\Row') {
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
     *
     * @param $key
     * @param $value
     * @return Table
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
     * @return \JsonServer\Row
     * @throws \OutOfRangeException
     */
    public function find($id)
    {
        $result = $this->filter(function ($item) use ($id) {
            return $item->id == $id;
        });
        if (count($result->rows) > 0) {
            return $result[0];
        } else {
            throw new \OutOfRangeException("there is no resource with id $id");
        }
    }

    /**
     * Filter rows by related entity id
     *
     * @param $parent - array with 'table' and 'id' keys
     * @return $this|Table
     */
    public function filterByParent($parent)
    {
        if ($parent) {
            $this->db->$parent['table']->find($parent['id']);
            return $this->where($this->getParentKeyName($parent['table']), $parent['id']);
        }
        return $this;
    }

    /**
     * Add new resource
     *
     * @param $data
     * @param string $parent
     * @return int
     */
    public function insert($data, $parent = null)
    {
        $row = new Row($data, $this);
        if ($parent) {
            $parentName = $this->getParentKeyName($parent['table']);
            $row->$parentName = $parent['id'];
        }
        $this->rows[] = $row;
        return $row;
    }

    /**
     * Update resource
     *
     * @param $id
     * @param $data
     * @return int
     * @throws \OutOfRangeException
     */
    public function update($id, $data)
    {
        $row = $this->find($id);
        $row->setData($data);
        return $row;
    }

    /**
     * Delete resource
     *
     * @param $id
     * @return int
     * @throws \OutOfRangeException
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
        if (count($ids) === 0) {
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
        $this->sort();
        $this->limits();
        $result = [];
        foreach ($this->rows as $row) {
            $result[] = $row->toArray();
        }
        return $result;
    }

    /**
     * Return json representation of table
     *
     * @return string
     */
    public function getContent()
    {
        return json_encode($this->toArray());
    }

    /**
     * Return count of rows in table
     *
     * @return int
     */
    public function count()
    {
        return count($this->rows);
    }

    /**
     * @return db
     */
    public function getDb()
    {
        return $this->db;
    }

    /**
     * @return mixed
     */
    public function getTabName()
    {
        return $this->tabName;
    }

    /**
     * @return array
     */
    public function getEmbeds()
    {
        return $this->embeds;
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
     * Set sorting field
     * @param $field
     * @return $this
     */
    public function _sort($field)
    {
        $this->sortField = $field;
        return $this;
    }


    /**
     * Set sorting order
     * @param $order
     * @return $this
     * @throws \DomainException
     */
    public function _order($order)
    {
        $order = mb_strtolower($order);
        if ($order !== 'asc' && $order !== 'desc') {
            throw new \DomainException ("Unknown sort type $order");
        }
        $this->sortOrder = $order;
        return $this;
    }

    /**
     * Set start param
     * @param $start
     * @return $this
     * @throws \DomainException
     */
    public function _start($start)
    {
        if (!is_numeric($start) || $start < 0) {
            throw new \DomainException ("Start '$start' must be a positive integer");
        }
        $this->start = $start;
        return $this;
    }

    /**
     * Set end param
     * @param $end
     * @return $this
     * @throws \DomainException
     */
    public function _end($end)
    {
        if (!is_numeric($end) || $end < 0) {
            throw new \DomainException ("end '$end'' must be a positive integer");
        }
        $this->end = $end;
        return $this;
    }

    /**
     * set limit param
     * @param $limit
     * @return $this
     * @throws \DomainException
     */
    public function _limit($limit)
    {
        if (!is_numeric($limit) || $limit < 0) {
            throw new \DomainException ("limit '$limit'' must be a positive integer");
        }
        $this->limit = $limit;
        return $this;
    }

    /**
     * filter rows with fulltext search
     * @param $q
     * @return Table
     */
    public function _query($q)
    {
        return $this->filter(function ($item) use ($q) {
            return $item->search($q) === true;
        });
    }

    /**
     * @param $embedStr
     * @return $this
     */
    public function _embed($embedStr)
    {
        $this->embeds = explode(',', $embedStr);
        return $this;
    }

    /**
     * Filter rows by $name field with $arguments[0] value
     * @param $name
     * @param $arguments
     * @return Table
     */
    public function __call($name, $arguments)
    {
        return $this->where($name, $arguments[0]);
    }

    /**
     *limit result
     */
    private function limits()
    {
        $offset = $this->start ?: 0;
        $length = max($this->end ? $this->end - $offset : null, $this->limit);
        $this->rows = array_slice($this->rows, $offset, $length);
    }

    /**
     * Sort rows in table
     */
    private function sort()
    {
        $sortField = $this->sortField;
        $sortOrder = $this->sortOrder;
        $sortFunc = function ($a, $b) use ($sortField, $sortOrder) {
            if ($a->$sortField === $b->$sortField) {
                return 0;
            }
            if ($a->$sortField > $b->$sortField) {
                return $sortOrder === 'asc' ? 1 : -1;
            }
            if ($a->$sortField < $b->$sortField) {
                return $sortOrder === 'asc' ? -1 : 1;
            }
        };
        usort($this->rows, $sortFunc);
    }

    /**
     * Filter rows with callback function
     *
     * @param $callback
     * @return static
     */
    private function filter($callback)
    {
        $table = clone $this;
        $table->rows = array_slice(array_filter($this->rows, $callback), 0);
        return $table;
    }

    /**
     * Return parent relation field in right form
     *
     * @param $noun
     * @return string
     */
    private function getParentKeyName($noun)
    {
        $method = Config::get('relationsNamingForm');
        return Inflector\Inflector::$method($noun) . "_id";
    }


}