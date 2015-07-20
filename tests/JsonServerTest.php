<?php


class JsonServerTest extends PHPUnit_Framework_TestCase
{

    protected $fixture;

    protected function tearDown()
    {
        $this->fixture->__destruct();
    }
    /**
     * @dataProvider requestProviderSingular
     */
    public function testRequestSingularSingular ($method, $url, $data, $expected)
    {
        $this->fixture = new \JsonServer\JsonServer(getcwd().'/tests/singularDB.json');
        $config = \JsonServer\Config::getInstance();
        $config->set('urlNamingForm', 'singularize');
        $config->set('tableNamingForm', 'singularize');
        $config->set('relationsNamingForm', 'singularize');
        $this->assertEquals($expected, $this->fixture->handleRequest($method, $url, $data));
    }

    /**
     * @dataProvider requestProviderSingular
     */
    public function testRequestSingularPlural ($method, $url, $data, $expected)
    {
        $this->fixture = new \JsonServer\JsonServer(getcwd().'/tests/pluralDB.json');
        $config = \JsonServer\Config::getInstance();
        $config->set('urlNamingForm', 'singularize');
        $config->set('tableNamingForm', 'pluralize');
        $config->set('relationsNamingForm', 'singularize');
        $this->assertEquals($expected, $this->fixture->handleRequest($method, $url,$data));
    }

    public function requestProviderSingular()
    {
        return [
            ['GET', ['post'], [], [['id'=>1, 'title' => 'json-server', 'author' => 'typicode'],['id'=>2, 'title' => 'json-server', 'author' => 'typicode']], 'get post'],
            ['GET', ['post/1'], [], ['id'=>1, 'title' => 'json-server', 'author' => 'typicode'], 'get post/1'],
            ['GET', ['post/1/comment/1'], [], ['id'=>1, 'body' => 'some comment', 'post_id' => 1], 'get post/1/comment/1'],
            ['GET', ['unknown'], [], [], 'get unknown'],
            ['GET', ['unknown/1'], [], [], 'get unknown/1'],
        ];
    }

    /**
     * @dataProvider requestProviderPlural
     */
    public function testRequestPluralSingular ($method, $url, $data, $expected, $msg)
    {
        $this->fixture = new \JsonServer\JsonServer(getcwd().'/tests/singularDB.json');
        $config = \JsonServer\Config::getInstance();
        $config->set('urlNamingForm', 'pluralize');
        $config->set('tableNamingForm', 'singularize');
        $config->set('relationsNamingForm', 'singularize');
        $this->assertEquals($expected, $this->fixture->handleRequest($method, $url, $data), $msg);
    }

    /**
     * @dataProvider requestProviderPlural
     */
    public function testRequestPluralPlural ($method, $url, $data, $expected, $msg)
    {
        $this->fixture = new \JsonServer\JsonServer(getcwd().'/tests/pluralDB.json');
        $config = \JsonServer\Config::getInstance();
        $config->set('urlNamingForm', 'pluralize');
        $config->set('tableNamingForm', 'pluralize');
        $config->set('relationsNamingForm', 'singularize');
        $this->assertEquals($expected, $this->fixture->handleRequest($method, $url, $data), $msg);
    }

    public function requestProviderPlural()
    {
        return [
            ['GET', ['posts'], [], [['id'=>1, 'title' => 'json-server', 'author' => 'typicode'],['id'=>2, 'title' => 'json-server', 'author' => 'typicode']], 'get posts'],
            ['GET', ['posts/1'], [], ['id'=>1, 'title' => 'json-server', 'author' => 'typicode'], 'get posts/1'],
            ['GET', ['posts/1/comments/1'], [], ['id'=>1, 'body' => 'some comment', 'post_id' => 1], 'get posts/1/comments/1'],
            ['GET', ['unknowns'], [], [], 'get unknowns'],
            ['GET', ['unknowns/1'], [], [], 'get unknowns/1'],
        ];
    }
}