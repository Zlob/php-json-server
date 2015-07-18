<?php


class JsonDataBaseTest extends PHPUnit_Framework_TestCase
{
    protected $fixture;

    protected function setUp()
    {
        $this->fixture = new \JsonServer\JsonDataBase(getcwd().'/tests/pluralDB.json');
    }

    protected function tearDown()
    {

        $this->fixture->__destruct();
    }

    public function testGetTable()
    {
        self::assertInstanceOf('JsonServer\JsonTable', $this->fixture->getTable('post'));
    }



}