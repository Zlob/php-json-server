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
    {
        $this->dbFile = fopen($pathToFile, 'r+b');
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

    function __destruct() {
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

    public function save()
    {
        $result = [];
        foreach($this->tables as $table){
            $result[]  = $table->toArray();
        }
        return json_encode($result);
    }

}