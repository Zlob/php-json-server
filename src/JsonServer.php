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
     * @return mixed
     */
    public function handleRequest($method, $uri, $data)
    {
        $this->data = $data;
        $this->uri = explode('/', $uri[0]);
        return $this->$method();
    }

    /**
     * handle GET request
     * @return mixed
     */
    public function GET()
    {
        $filter = $this->getFilters($this->uri);
        $last = array_pop($filter);
        $parent = count($filter) > 0 ? array_pop($filter) : null;
        $tabName = $this->prepareForm($last['table']);
        $id = $last['id'];
        if ($id) {
            $result = $this->jsonDb->$tabName->find($id);
        } else {
            $result = $this->jsonDb->$tabName->filterByParent($parent);
        }
        if ($result) {
            return $result->toArray();
        } else {
            return [];
        }
    }

    /**
     *handle POST request - create new resource
     */
    public function POST()
    {
        $filter = $this->getFilters($this->uri);
        $last = array_pop($filter);
        $tabName = $this->prepareForm($last['table']);
        $id = $last['id'];
        if (!$id) {
            $table = $this->jsonDb->$tabName;
            $table->post($this->data);
        } else {
            //todo check returned single resource - id must not be specified
        }
    }

    /**
     * handle PATCH request - update existing resource
     */
    public function PATCH()
    {
        $filter = $this->getFilters($this->uri);
        $last = array_pop($filter);
        $tabName = $this->prepareForm($last['table']);
        $id = $last['id'];
        if ($id) {
            $table = $this->jsonDb->$tabName;
            $table->patch($id, $this->data);
        } else {
            //todo check - id must be specified
        }
    }

    /**
     * handle DELETE request - delete resource
     */
    public function DELETE()
    {
        $filter = $this->getFilters($this->uri);
        $last = array_pop($filter);
        $tabName = $this->prepareForm($last['table']);
        $id = $last['id'];
        if ($id) {
            $table = $this->jsonDb->$tabName;
            $table->delete($id, $this->data);
        } else {
            //todo check - id must be specified
        }
    }

    /**
     * get right form of table based on config
     * @param $noun
     * @return mixed
     */
    private function prepareForm($noun)
    {
        //todo single global method
        if (!(Config::get('urlNamingForm') === Config::get('tableNamingForm'))) {
            //todo another method
            $method = Config::get('tableNamingForm') . 'ize';
            return Inflector\Inflector::$method($noun);
        } else {
            return $noun;
        }
    }

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
}
