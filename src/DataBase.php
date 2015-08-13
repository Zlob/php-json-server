<?php

namespace JsonServer;

/**
 * Class JsonDataBase
 * @package JsonServer
 */
class DataBase
{

    /**
     * dataBase file
     *
     * @var resource
     */
    private $dbFile;

    /**
     * Array of tables in DB
     *
     * @var array
     */
    private $tables = [];

    /**
     * Create instance of JsonDataBase
     *
     * @param $pathToFile
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    public function __construct($pathToFile)
    {
        $this->dbFile = fopen($pathToFile, 'r+b');
        if (!$this->dbFile) {
            throw new \RuntimeException("cannot open file $pathToFile");
        }
        flock($this->dbFile, LOCK_EX);
        $jsonString = filesize($pathToFile) > 0 ? fread($this->dbFile, filesize($pathToFile)) : "";
        if (is_string($jsonString)) {
            $tables = json_decode($jsonString, true);
            if (is_array($tables)) {
                foreach (json_decode($jsonString, true) as $tableName => $tableData) {
                    $this->tables[$tableName] = new Table($tableData, $tableName, $this);
                }
            }
        } else {
            throw new \InvalidArgumentException('data should be JSON string');
        }
    }

    /**
     * Unlock file
     */
    public function __destruct()
    {
        flock($this->dbFile, LOCK_UN);
    }

    /**
     * Save changes into db file
     *
     * @return string
     */
    public function save()
    {
        $result = [];
        foreach ($this->tables as $tabName => $table) {
            $result[$tabName] = $table->toArray();
        }

        ftruncate($this->dbFile, 0);
        rewind($this->dbFile);
        fwrite($this->dbFile, json_encode($result, JSON_PRETTY_PRINT));
    }

    /**
     * Return table instance by name
     *
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        if (array_key_exists($name, $this->tables)) {
            return $this->tables[$name];
        } else {
            $this->tables[$name] = new Table([], $name, $this);
            return $this->tables[$name];
        }
    }
}