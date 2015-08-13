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
    public function testRequestSingularSingular($method, $url, $data, $expected, $msg)
    {
        $config = \JsonServer\Config::getInstance();
        $config->set('urlNamingForm', 'singularize');
        $config->set('tableNamingForm', 'singularize');
        $config->set('relationsNamingForm', 'singularize');
        $config->set('pathToDb', '/tests/Mock/singularDB.json');
        $this->fixture = new \JsonServer\JsonServer();
        $r = $this->fixture->handleRequest($method, $url, $data);
        $this->assertEquals($expected, $r->getContent(), $msg);
    }

    /**
     * @dataProvider requestProviderSingular
     */
    public function testRequestSingularPlural($method, $url, $data, $expected, $msg)
    {
        $config = \JsonServer\Config::getInstance();
        $config->set('urlNamingForm', 'singularize');
        $config->set('tableNamingForm', 'pluralize');
        $config->set('relationsNamingForm', 'singularize');
        $config->set('pathToDb', '/tests/Mock/pluralDB.json');
        $this->fixture = new \JsonServer\JsonServer();
        $r = $this->fixture->handleRequest($method, $url, $data);
        $this->assertEquals($expected, $r->getContent(), $msg);
    }

    public function requestProviderSingular()
    {
        return [
            ['GET', 'post', [], '[{"id":1,"author":"zlob","title":"json-server"},{"id":2,"author":"zlob","title":"json-server"}]', 'get post'],
            ['GET', 'post/1', [], '{"id":1,"author":"zlob","title":"json-server"}', 'get post/1'],
            ['GET', 'post/1/comment/1', [], '{"id":1,"body":"some comment","post_id":1}', 'get post/1/comment/1'],
            ['GET', 'unknown', [], '[]', 'get unknown'],
        ];
    }

    /**
     * @dataProvider requestProviderPlural
     */
    public function testRequestPluralSingular($method, $url, $data, $expected, $msg)
    {
        $config = \JsonServer\Config::getInstance();
        $config->set('urlNamingForm', 'pluralize');
        $config->set('tableNamingForm', 'singularize');
        $config->set('relationsNamingForm', 'singularize');
        $config->set('pathToDb', '/tests/Mock/singularDB.json');
        $this->fixture = new \JsonServer\JsonServer();
        $r = $this->fixture->handleRequest($method, $url, $data);
        $this->assertEquals($expected, $r->getContent(), $msg);
    }

    /**
     * @dataProvider requestProviderPlural
     */
    public function testRequestPluralPlural($method, $url, $data, $expected, $msg)
    {
        $config = \JsonServer\Config::getInstance();
        $config->set('urlNamingForm', 'pluralize');
        $config->set('tableNamingForm', 'pluralize');
        $config->set('relationsNamingForm', 'singularize');
        $config->set('pathToDb', '/tests/Mock/pluralDB.json');
        $this->fixture = new \JsonServer\JsonServer();
        $r = $this->fixture->handleRequest($method, $url, $data);
        $this->assertEquals($expected, $r->getContent(), $msg);
    }

    public function requestProviderPlural()
    {
        return [
            ['GET', 'posts', [], '[{"id":1,"author":"zlob","title":"json-server"},{"id":2,"author":"zlob","title":"json-server"}]', 'get posts'],
            ['GET', 'posts/1', [], '{"id":1,"author":"zlob","title":"json-server"}', 'get posts/1'],
            ['GET', 'posts/1/comments/1', [], '{"id":1,"body":"some comment","post_id":1}', 'get posts/1/comments/1'],
            ['GET', 'unknowns', [], '[]', 'get unknowns'],
        ];
    }
}