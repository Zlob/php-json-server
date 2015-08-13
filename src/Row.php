<?php

namespace JsonServer;

/**
 * Class JsonRow
 * @package JsonServer
 */
class Row
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
     * @var string
     */
    private $table = '';

    private $embeddedResources = [];


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
        $this->fields[$key] = $value;
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
        if (Config::get('fieldsAutoSorting')) {
            uksort($this->fields, Config::get('fieldsAutoSortingFunc'));
        }
        if ($this->table) {
            $this->embedResources($this->table->getEmbeds());
        }
        $result = $this->fields;
        foreach ($this->embeddedResources as $resName => $resource) {
            $result[$resName] = $resource->toArray();
        }
        return $result;
    }


    /**
     * Return json representation of row
     *
     * @return string
     */
    public function getContent()
    {
        return json_encode($this->toArray());
    }

    /**
     * Search substring in all row fields
     * @param $q
     * @return bool
     */
    public function search($q)
    {
        foreach ($this->fields as $field) {
            if (strpos($field, $q) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Fill row with embed resources
     *
     * @param $resources
     */
    public function embedResources($resources)
    {
        if ($resources) {
            foreach ($resources as $resource) {
                $tabName = $this->table->getTabName();
                $this->embeddedResources[$resource] = $this->table->getDb()->$resource->filterByParent(['table' => $tabName, 'id' => $this->id]);
            }
        }
    }

}