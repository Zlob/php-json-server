<?php

namespace JsonServer;

use BadFunctionCallException;
use Doctrine\Common\Inflector;
use Symfony\Component\HttpFoundation\Response as Response;

/**
 * Class JsonServer
 * @package JsonServer
 */
class JsonServer
{

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
        $this->jsonDb = new DataBase(__DIR__ . '/..' . Config::get('pathToDb'));
        $this->response = new Response();
        $this->response->headers->set('Content-Type', 'application/vnd.api+json');
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
        $params = explode('/', $uri);
        $filter = $this->getFilters($params);
        $last = array_pop($filter);
        $tabName = $last['table'];
        $id = $last['id'];
        $parent = $this->getParent($filter);
        return $this->$method($tabName, $id, $parent, $data);
    }

    /**
     * Handle GET request
     *
     * @return mixed
     */
    public function GET($tabName, $id, $parent, $data)
    {
        try {
            $result = $this->jsonDb->$tabName->filterByParent($parent);
            $result = $this->processFilters($result);
            //fetching single resource
            if ($id) {
                $result = $result->find($id);
                $this->response->setContent($result->getContent());
                $this->response->setStatusCode(200);
            } //fetching resource collection
            else {
                $this->response->setContent($result->getContent());
                $this->response->headers->set('X-Total-Count', $result->count());
                $this->response->setStatusCode(200);
            }
        } catch (\OutOfRangeException $e) {
            return call_user_func(Config::get('resourceNotFound'), $this->response);
        }
        return $this->response;
    }

    /**
     * Handle POST request - create new resource
     */
    public function POST($tabName, $id, $parent, $data)
    {
        if (!$id) {
            $table = $this->jsonDb->$tabName;
            $row = $table->insert($data, $parent);
            $this->jsonDb->save();
            $this->response->setContent($row->getContent());
            $this->response->setStatusCode(201);
            return $this->response;
        } else {
            throw new BadFunctionCallException('path must not contaign id');
        }
    }

    /**
     * Handle PATCH request - update part of existing resource
     */
    public function PATCH($tabName, $id, $parent, $data)
    {
        if ($id) {
            $table = $this->jsonDb->$tabName;
            try {
                $row = $table->update($id, $data);
                $this->jsonDb->save();
                $this->response->setContent($row->getContent());
                $this->response->setStatusCode(200);
                return $this->response;
            } catch (\OutOfRangeException $e) {
                $this->response->setContent('');
                $this->response->setStatusCode(404);
                return $this->response;
            }

        } else {
            throw new BadFunctionCallException('path must contaign id');
        }
    }

    /**
     * Handle PUT request - update existing resource. behavior same to PATCH
     */
    public function PUT($tabName, $id, $parent, $data)
    {
        return $this->PATCH($tabName, $id, $parent, $data);
    }

    /**
     * Handle DELETE request - delete resource
     */
    public function DELETE($tabName, $id, $parent, $data)
    {
        if ($id) {
            $table = $this->jsonDb->$tabName;
            try {
                $table->delete($id);
                $this->jsonDb->save();
                $this->response->setContent('');
                $this->response->setStatusCode(204);
            } catch (\OutOfRangeException $e) {
                $this->response->setContent('');
                $this->response->setStatusCode(404);
                return $this->response;
            }

        } else {
            throw new BadFunctionCallException('path must contaign id');
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
                $elem['table'] = $this->prepareForm($tab);
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
