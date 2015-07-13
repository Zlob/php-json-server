<?php

namespace JsonServer;

class JsonDataBase
{
    private $tables = [];

    public function __construct($jsonString)
    {
        if (is_string($jsonString)) {
            $tables = json_decode($jsonString, true);
            if (is_array($tables)) {
                foreach (json_decode($jsonString, true) as $tableName => $tableData) {
                    $this->tables[$tableName] = new JsonTable($tableData);
                }
            } else {
                throw new \InvalidArgumentException('data should be JSON string');
            }
        } else {
            throw new \InvalidArgumentException('data should be JSON string');
        }
    }

    public function getTable($tableName)
    {
        if (array_key_exists($tableName, $this->tables)) {
            return $this->tables[$tableName];
        } else {
            $this->tables[$tableName] = new JsonTable([]);
            return $this->tables[$tableName];
        }
    }

}