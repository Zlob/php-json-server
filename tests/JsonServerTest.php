<?php


class JsonServerTest extends PHPUnit_Framework_TestCase
{
    protected $fixture;

    protected function setUp()
    {
        require '/vendor/autoload.php';

        $dbPath = getcwd().'/tests/db.json';

        $this->fixture = new \JsonServer\JsonServer($dbPath);

    }

    protected function tearDown()
    {
        $this->fixture = NULL;
    }

    public function testConstructorPassed()
    {
        $dbPath = getcwd().'/tests/db.json';
         new \JsonServer\JsonServer($dbPath);
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage url should contain at least table name
     */
    public function testHandleRequestGetEmpty()
    {
        $this->fixture->handleRequest('GET', [''], '');
    }

    public function testHandleRequestGetSingularAll()
    {
        $expect = [["id"=>1, "title" => "json-server", "author" => "typicode"],["id"=>2, "title" => "json-server", "author" => "typicode"]];
        $result = $this->fixture->handleRequest('GET', ['post'], '');
        self::assertEquals($expect, $result);
    }

    public function testHandleRequestGetSingularById()
    {
        $expect = ["id"=>1, "title" => "json-server", "author" => "typicode"];
        $result = $this->fixture->handleRequest('GET', ['post/1'], '');
        self::assertEquals($expect, $result);
    }

    public function testHandleRequestGetSingularWithParentAll()
    {
        $expect = [["id"=>1, "body" => "some comment", "post_id" => 1]];
        $result = $this->fixture->handleRequest('GET', ['post/1/comment/'], '');
        self::assertEquals($expect, $result);
    }

    public function testHandleRequestGetSingularWithParentById()
    {
        $expect = ["id"=>1, "body" => "some comment", "post_id" => 1];
        $result = $this->fixture->handleRequest('GET', ['post/1/comment/1'], '');
        self::assertEquals($expect, $result);
    }


}