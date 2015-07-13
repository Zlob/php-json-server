<?php

namespace JsonServer;

use Doctrine\Common\Inflector;

class JsonServer
{
    private $uri = [];
    private $filter;
    private $jsonDb;
    private $config;

    public function __construct($dbPath)
    {
        $this->config = new Config();
        $this->jsonDb = new JsonDataBase(file_get_contents($dbPath));
    }

    public function handleRequest($method, $uri, $filter)
    {
        $this->uri = explode('/', $uri[0]);
        $this->filter = $filter;
        return $this->$method();
    }

    public function GET()
    {
        return $this->getObject();
    }

    public function POST()
    {
//        return $this->getObject();
    }

    public function PUT()
    {
//        return $this->getObject();
    }

    public function DELETE()
    {
//        return $this->getObject();
    }


    public function getObject($parentName = null, $parentId = null)
    {
        $table = $this->prepareForm(array_shift($this->uri));
        $id = array_shift($this->uri);
        //запрос по id
        if ($id) {
            $result = $this->getOne($table, $id, $parentName, $parentId);
            if ($result && count($this->uri) >= 1) {
                return $this->getObject($table, $id);
            }
        } //запрос всех по типу
        elseif ($table) {
            $result = $this->getMany($table, $parentName, $parentId);
        } else {
            throw new \InvalidArgumentException('url should contain at least table name');
        }
        return $result->toArray();
    }

    private function getOne($table, $id, $parentName = null, $parentId = null)
    {
        $tab = $this->jsonDb->getTable($table)->filterByParent($parentName, $parentId)
            ->find($id);
        return $tab;
    }

    private function getMany($table, $parentName = null, $parentId = null)
    {
        $tab = $this->jsonDb->getTable($table)->filterByParent($parentName, $parentId);
        return $tab;
    }

    private function prepareForm($noun){
        if (!($this->config['urlNamingForm'] === $this->config['tableNamingForm'] )){
            $method = $this->config['tableNamingForm'].'ize';
            return Inflector\Inflector::$method($noun);
        }
        else{
            return $noun;
        }
    }
}
