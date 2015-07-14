<?php


class JsonServerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider requestProviderSingular
     */
    public function testRequestSingularSingular ($method, $url, $filter, $expected)
    {
        $server = new \JsonServer\JsonServer(getcwd().'/tests/singularDB.json');
        $config = \JsonServer\Config::getInstance();
        $config->set("urlNamingForm", "singular");
        $config->set("tableNamingForm", "singular");
        $config->set("relationsNamingForm", "singular");
        $this->assertEquals($expected, $server->handleRequest($method, $url, $filter));
    }

    /**
     * @dataProvider requestProviderSingular
     */
    public function testRequestSingularPlural ($method, $url, $filter, $expected)
    {
        $server = new \JsonServer\JsonServer(getcwd().'/tests/pluralDB.json');
        $config = \JsonServer\Config::getInstance();
        $config->set("urlNamingForm", "singular");
        $config->set("tableNamingForm", "plural");
        $config->set("relationsNamingForm", "singular");
        $this->assertEquals($expected, $server->handleRequest($method, $url, $filter));
    }

    public function requestProviderSingular()
    {
        return [
            ['GET', ['post'], '', [["id"=>1, "title" => "json-server", "author" => "typicode"],["id"=>2, "title" => "json-server", "author" => "typicode"]]],
            ['GET', ['post/1'], '', ["id"=>1, "title" => "json-server", "author" => "typicode"]],
            ['GET', ['post/1/comment/1'], '', ["id"=>1, "body" => "some comment", "post_id" => 1]],
            ['GET', ['unknown'], '', []],
            ['GET', ['unknown/1'], '', []],
        ];
    }

    /**
     * @dataProvider requestProviderPlural
     */
    public function testRequestPluralSingular ($method, $url, $filter, $expected)
    {
        $server = new \JsonServer\JsonServer(getcwd().'/tests/singularDB.json');
        $config = \JsonServer\Config::getInstance();
        $config->set("urlNamingForm", "plural");
        $config->set("tableNamingForm", "singular");
        $config->set("relationsNamingForm", "singular");
        $this->assertEquals($expected, $server->handleRequest($method, $url, $filter));
    }

    /**
     * @dataProvider requestProviderPlural
     */
    public function testRequestPluralPlural ($method, $url, $filter, $expected)
    {
        $server = new \JsonServer\JsonServer(getcwd().'/tests/pluralDB.json');
        $config = \JsonServer\Config::getInstance();
        $config->set("urlNamingForm", "plural");
        $config->set("tableNamingForm", "plural");
        $config->set("relationsNamingForm", "singular");
        $this->assertEquals($expected, $server->handleRequest($method, $url, $filter));
    }

    public function requestProviderPlural()
    {
        return [
            ['GET', ['posts'], '', [["id"=>1, "title" => "json-server", "author" => "typicode"],["id"=>2, "title" => "json-server", "author" => "typicode"]]],
            ['GET', ['posts/1'], '', ["id"=>1, "title" => "json-server", "author" => "typicode"]],
            ['GET', ['posts/1/comments/1'], '', ["id"=>1, "body" => "some comment", "post_id" => 1]],
            ['GET', ['unknowns'], '', []],
            ['GET', ['unknowns/1'], '', []],
        ];
    }

    public function testConstructorPassed()
    {
        $dbPath = getcwd().'/tests/pluralDB.json';
         new \JsonServer\JsonServer($dbPath);
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage url should contain at least table name
     */
    public function testHandleRequestGetEmpty()
    {
        $dbPath = getcwd().'/tests/pluralDB.json';
        $server = new \JsonServer\JsonServer($dbPath);
        $server->handleRequest('GET', [''], '');
    }
}