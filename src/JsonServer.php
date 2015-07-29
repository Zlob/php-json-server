<?php

namespace JsonServer;

use BadFunctionCallException;
use Doctrine\Common\Inflector;

/**
 * Class JsonServer
 * @package JsonServer
 */
class JsonServer
{
    /**
     * Array of entities and ids, that need to retrieve
     *
     * @var array
     */
    private $params = [];

    /**
     * Path to resource
     *
     * @var string
     */
    private $path = "";

    /**
     * Request data
     *
     * @var array
     */
    private $data = [];

    /**
     * jsonDataBase instance
     *
     * @var DataBase
     */
    private $jsonDb;

    private $response;


    /**
     * Create new JsonServer instance
     */
    public function __construct()
    {
        $this->jsonDb = new DataBase(__DIR__ . Config::get('pathToDb'));
        $this->response = new JsonServerResponse();
    }

    /**
     * Remove jsonDb and unlock db file
     */
    public function __destruct()
    {
        $this->jsonDb->__destruct();
    }

    /**
     * Handle retrieved request
     *
     * @param $method
     * @param $uri
     * @param $data
     * @return mixed
     */
    public function handleRequest($method, $uri, $data = [])
    {
        $this->data = $data;
        $this->path = $uri;
        $this->params = explode('/', $uri);
        return $this->$method();
    }

    /**
     * Handle GET request
     *
     * @return mixed
     */
    public function GET()
    {
        $filter = $this->getFilters($this->params);
        $last = array_pop($filter);
        $parent = count($filter) > 0 ? array_pop($filter) : null;
        $tabName = $this->prepareForm($last['table']);
        $id = $last['id'];
        //fetching single resource
        if ($id) {
            try {
                $result = $this->processFilters($this->jsonDb->$tabName)->find($id);
                $this->response->data = $result->toArray();
                $this->response->status = 200;
            } catch (\OutOfRangeException $e) {
                $this->response = call_user_func(Config::get('resourceNotFound'), $this->response);
            }
        } //fetching resource collection
        else {
            $result = $this->jsonDb->$tabName->filterByParent($parent);
            $result = $this->processFilters($result);
            $this->response->data = $result->toArray();
            $this->response->status = 200;
        }
        return $this->response;
    }

    /**
     * Handle POST request - create new resource
     */
    public function POST()
    {
        $filter = $this->getFilters($this->params);
        $last = array_pop($filter);
        $tabName = $this->prepareForm($last['table']);
        $id = $last['id'];
        if (!$id) {
            $table = $this->jsonDb->$tabName;
            $row = $table->insert($this->data, $this->getParent($filter));
            $this->jsonDb->save();
            $this->response->data = $row->toArray();
            $this->response->status = 201;
            return $this->response;
        } else {
            throw new BadFunctionCallException("path $this->path must not contaign id");
        }
    }

    /**
     * Handle PATCH request - update part of existing resource
     */
    public function PATCH()
    {
        $filter = $this->getFilters($this->params);
        $last = array_pop($filter);
        $tabName = $this->prepareForm($last['table']);
        $id = $last['id'];
        if ($id) {
            $table = $this->jsonDb->$tabName;
            try {
                $row = $table->update($id, $this->data);
                $this->jsonDb->save();
                $this->response->data = $row->toArray();
                $this->response->status = 200;
                return $this->response;
            } catch (\OutOfRangeException $e) {
                $this->response->data = null;
                $this->response->status = 404 ;
            }

        } else {
            throw new BadFunctionCallException("path $this->path must contaign id");
        }
    }

    /**
     * Handle PUT request - update existing resource. behavior same to PATCH
     */
    public function PUT()
    {
        return $this->PATCH();
    }

    /**
     * Handle DELETE request - delete resource
     */
    public function DELETE()
    {
        $filter = $this->getFilters($this->params);
        $last = array_pop($filter);
        $tabName = $this->prepareForm($last['table']);
        $id = $last['id'];
        if ($id) {
            $table = $this->jsonDb->$tabName;
            try{
                $table->delete($id, $this->data);
                $this->jsonDb->save();
                $this->response->data = null;
                $this->response->status = 204;
            } catch (\OutOfRangeException $e) {
                $this->response->data = null;
                $this->response->status = 403;
            }

        } else {
            throw new BadFunctionCallException("path $this->path must contaign id");
        }
    }

    /**
     * Get right form of table based on config
     *
     * @param $noun
     * @return mixed
     */
    private function prepareForm($noun)
    {
        if (!(Config::get('urlNamingForm') === Config::get('tableNamingForm'))) {
            $method = Config::get('tableNamingForm');
            return Inflector\Inflector::$method($noun);
        } else {
            return $noun;
        }
    }

    /**
     * Create array of pairs table->id
     *
     * @param $uri
     * @return array
     */
    private function getFilters($uri)
    {
        $result = [];
        for ($i = 0; $i < count($uri); $i += 2) {
            $tab = array_key_exists($i, $uri) ? $uri[$i] : null;
            if ($tab) {
                $elem = [];
                $elem['table'] = $tab;
                $elem['id'] = array_key_exists($i + 1, $uri) ? $uri[$i + 1] : null;
                $result[] = $elem;
            }
        }
        return $result;
    }

    /**
     * Get parent filter
     * @param $filter
     * @return mixed|null
     */
    private function getParent($filter)
    {
        return count($filter) === 0 ? null : array_pop($filter);
    }

    /**
     * Filter rows by input data
     * @param $rows
     * @return mixed
     */
    private function processFilters($rows)
    {
        foreach ($this->data as $key => $value) {
            $rows = $rows->$key($value);
        }
        return $rows;
    }

}
