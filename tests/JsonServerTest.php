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
    public function testRequestSingularSingular ($method, $url, $data, $filter, $expected)
    {
        $this->fixture = new \JsonServer\JsonServer(getcwd().'/tests/singularDB.json');
        $config = \JsonServer\Config::getInstance();
        $config->set("urlNamingForm", "singular");
        $config->set("tableNamingForm", "singular");
        $config->set("relationsNamingForm", "singular");
        $this->assertEquals($expected, $this->fixture->handleRequest($method, $url, $data, $filter));
    }

    /**
     * @dataProvider requestProviderSingular
     */
    public function testRequestSingularPlural ($method, $url, $data, $filter, $expected)
    {
        $this->fixture = new \JsonServer\JsonServer(getcwd().'/tests/pluralDB.json');
        $config = \JsonServer\Config::getInstance();
        $config->set("urlNamingForm", "singular");
        $config->set("tableNamingForm", "plural");
        $config->set("relationsNamingForm", "singular");
        $this->assertEquals($expected, $this->fixture->handleRequest($method, $url,$data,$filter));
    }

    public function requestProviderSingular()
    {
        return [
            ['GET', ['post'], [], '', [["id"=>1, "title" => "json-server", "author" => "typicode"],["id"=>2, "title" => "json-server", "author" => "typicode"]]],
            ['GET', ['post/1'], [], '', ["id"=>1, "title" => "json-server", "author" => "typicode"]],
            ['GET', ['post/1/comment/1'], [], '', ["id"=>1, "body" => "some comment", "post_id" => 1]],
            //todo
//            ['GET', ['unknown'], [], '', []],
//            ['GET', ['unknown/1'], [], '', []],
        ];
    }

    /**
     * @dataProvider requestProviderPlural
     */
    public function testRequestPluralSingular ($method, $url, $data, $filter, $expected)
    {
        $this->fixture = new \JsonServer\JsonServer(getcwd().'/tests/singularDB.json');
        $config = \JsonServer\Config::getInstance();
        $config->set("urlNamingForm", "plural");
        $config->set("tableNamingForm", "singular");
        $config->set("relationsNamingForm", "singular");
        $this->assertEquals($expected, $this->fixture->handleRequest($method, $url, $data, $filter));
    }

    /**
     * @dataProvider requestProviderPlural
     */
    public function testRequestPluralPlural ($method, $url, $data, $filter, $expected)
    {
        $this->fixture = new \JsonServer\JsonServer(getcwd().'/tests/pluralDB.json');
        $config = \JsonServer\Config::getInstance();
        $config->set("urlNamingForm", "plural");
        $config->set("tableNamingForm", "plural");
        $config->set("relationsNamingForm", "singular");
        $this->assertEquals($expected, $this->fixture->handleRequest($method, $url, $data, $filter));
    }

    public function requestProviderPlural()
    {
        return [
            ['GET', ['posts'], [],'', [["id"=>1, "title" => "json-server", "author" => "typicode"],["id"=>2, "title" => "json-server", "author" => "typicode"]]],
            ['GET', ['posts/1'], [],'', ["id"=>1, "title" => "json-server", "author" => "typicode"]],
            ['GET', ['posts/1/comments/1'], [], '', ["id"=>1, "body" => "some comment", "post_id" => 1]],
            //todo
//            ['GET', ['unknowns'], [], '', []],
//            ['GET', ['unknowns/1'], [], '', []],
        ];
    }


    //todo
//    /**
//     * @expectedException        InvalidArgumentException
//     * @expectedExceptionMessage url should contain at least table name
//     */
//    public function testHandleRequestGetEmpty()
//    {
//        $dbPath = getcwd().'/tests/pluralDB.json';
//        $this->fixture = new \JsonServer\JsonServer($dbPath);
//        $this->fixture->handleRequest('GET', [''], [], '');
//    }
}