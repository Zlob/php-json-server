<?php

namespace JsonServer;

/**
 * Class JsonDataBase
 * @package JsonServer
 */
class JsonDataBase
{

    private $dbFile;
    /**
     * array of tables in DB
     * @var array
     */
    private $tables = [];

    /**
     * create instance of JsonDataBase
     * @param $pathToFile
     * @internal param $jsonString
     */
    public function __construct($pathToFile)
    {   //todo return code
        $this->dbFile = fopen($pathToFile, 'r+b');
        //todo return code
        flock($this->dbFile, LOCK_EX);
        $jsonString = fread($this->dbFile, filesize($pathToFile));
        if (is_string($jsonString)) {
            $tables = json_decode($jsonString, true);
            if (is_array($tables)) {
                foreach (json_decode($jsonString, true) as $tableName => $tableData) {
                    $this->tables[$tableName] = new JsonTable($tableData, $this);
                }
            } else {
                throw new \InvalidArgumentException('data should be JSON string');
            }
        } else {
            throw new \InvalidArgumentException('data should be JSON string');
        }
    }

    function __destruct()
    {
        flock($this->dbFile, LOCK_UN);
    }

    /**
     * return table
     * @param $tableName
     * @return mixed
     */
    public function getTable($tableName)
    {
        if (array_key_exists($tableName, $this->tables)) {
            return $this->tables[$tableName];
        } else {
            $this->tables[$tableName] = new JsonTable([]);
            return $this->tables[$tableName];
        }
    }

    /**
     * save changes into db file
     * @return string
     */
    public function save()
    {
        $result = [];
        foreach ($this->tables as $tabName => $table) {
            $result[$tabName] = $table->toArray();
        }
        //todo return code
        ftruncate($this->dbFile, 0);
        rewind($this->dbFile);
        fwrite($this->dbFile, json_encode($result, JSON_PRETTY_PRINT));
    }

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return array_key_exists($this->tables, $offset);
    }

    public function __get($key)
    {
        if (array_key_exists($key, $this->tables)) {
            return $this->tables[$key];
        } else {
            $this->tables[$key] = new JsonTable([], $this);
            return $this->tables[$key];
        }
    }
}