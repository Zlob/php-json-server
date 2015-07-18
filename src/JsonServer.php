<?php

namespace JsonServer;

use Doctrine\Common\Inflector;

/**
 * Class JsonServer
 * @package JsonServer
 */
class JsonServer
{
    /**
     * array of entities and ids,  that need to retrieve
     * @var array
     */
    private $uri = [];

    /**
     * array of request data
     * @var array
     */
    private $data = [];

    /**
     * additional filters
     * @var
     */
    private $filter;

    /**
     * jsonDataBase instance
     * @var JsonDataBase
     */
    private $jsonDb;


    /**
     * create new JsonServer instance
     * @param $dbPath
     */
    public function __construct($dbPath)
    {
        $this->jsonDb = new JsonDataBase($dbPath);
    }

    /**
     * create new JsonServer instance
     * @param $dbPath
     */
    public function __destruct()
    {
        $this->jsonDb->__destruct();
    }

    /**
     * Handle retrieved request
     * @param $method
     * @param $uri
     * @param $data
     * @param $filter
     * @return mixed
     */
    public function handleRequest($method, $uri, $data, $filter)
    {
        $this->data = $data;
        $this->uri = explode('/', $uri[0]);
        $this->filter = $filter;
        return $this->$method();
    }

    /**
     * handle GET request
     * @return mixed
     */
    public function GET()
    {
        $result = $this->getObject();
        if ($result) {
            return $result->toArray();
        } else {
            return [];
        }
    }

    /**
     *handle POST request
     */
    public function POST()
    {
        $result = $this->getObject();
        $result->save();
        if ($result) {
            return $result->toArray();
        } else {
            return [];
        }
    }

    /**
     * handle PUT request
     */
    public function PUT()
    {
//        return $this->getObject();
    }

    /**
     * handle DELETE request
     */
    public function DELETE()
    {
//        return $this->getObject();
    }


    /**
     * get rows from DB
     * @param null $parentName
     * @param null $parentId
     * @return mixed
     */
    private function getObject($parentName = null, $parentId = null)
    {
        $table = array_shift($this->uri);
        $id = array_shift($this->uri);
        //get by id
        if ($id) {
            $result = $this->getOne($table, $id, $parentName, $parentId);
            if ($result && count($this->uri) >= 1) {
                return $this->getObject($table, $id);
            }
        } //get all by table
        elseif ($table) {
            $result = $this->getMany($table, $parentName, $parentId);
        } else {
            throw new \InvalidArgumentException('url should contain at least table name');
        }

        //todo jsonApi - если запись не найдена - вернуть 404 или  200 OK response with null as the primary data
        return $result;

    }

    /**
     * return single row from table by params
     * @param $table
     * @param $id
     * @param null $parentName
     * @param null $parentId
     * @return mixed
     */
    private function getOne($table, $id, $parentName = null, $parentId = null)
    {
        $table = $this->prepareForm($table);
        $tab = $this->jsonDb->getTable($table)->filterByParent($parentName, $parentId)
            ->find($id);
        return $tab;
    }

    /**
     * return all rows from table by params
     * @param $table
     * @param null $parentName
     * @param null $parentId
     * @return mixed
     */
    private function getMany($table, $parentName = null, $parentId = null)
    {
        $table = $this->prepareForm($table);
        $tab = $this->jsonDb->getTable($table)->filterByParent($parentName, $parentId);
        return $tab;
    }

    /**
     * get right form of table based on config
     * @param $noun
     * @return mixed
     */
    private function prepareForm($noun)
    {
        if (!(Config::get('urlNamingForm') === Config::get('tableNamingForm'))) {
            //todo another method
            $method = Config::get('tableNamingForm') . 'ize';
            return Inflector\Inflector::$method($noun);
        } else {
            return $noun;
        }
    }
}
