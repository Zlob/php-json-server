<?php

namespace JsonServer;

use Doctrine\Common\Inflector;

class JsonTable implements \ArrayAccess
{
    private $rows = [];

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

    public function where($key, $value)
    {
        return $this->filter(function ($item) use ($key, $value) {
            return $item->$key == $value;
        });
    }

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

    public function filterByParent($parentName, $parentId)
    {
        if ($parentName != null) {
            return $this->where($this->getParentKeyName($parentName), $parentId);
        }
        return $this;
    }

    public function toArray()
    {
        $result = [];
        foreach ($this->rows as $row) {
            $result[] = $row->toArray();
        }
        return $result;
    }

    public function offsetExists($offset)
    {
        return array_key_exists($this->rows, $offset);
    }

    public function offsetGet($offset)
    {
        return $this->rows[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $this->rows[] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->rows[$offset]);
    }

    private function filter($callback)
    {
        return new static(array_filter($this->rows, $callback));
    }

    private function getParentKeyName($parentName)
    {
        $parentName = Inflector\Inflector::singularize($parentName);
        return $parentName . "_id";
    }
}