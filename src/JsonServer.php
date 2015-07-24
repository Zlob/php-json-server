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
     * @var JsonDataBase
     */
    private $jsonDb;


    /**
     * Create new JsonServer instance
     */
    public function __construct()
    {
        $this->jsonDb = new JsonDataBase(__DIR__.Config::get('pathToDb'));
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
    public function handleRequest($method, $uri, $data)
    {
        $this->data = $data;
        $this->path = $uri;  //todo maybe throw exception if $uri === "" ??!
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
            $row = $table->insert($this->data);
            $this->jsonDb->save();
            return $row->toArray();
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
            $row = $table->update($id, $this->data);
            $this->db->save();
            return $row->toArray();
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
            $table->delete($id, $this->data);
            $this->db->save();
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
}
