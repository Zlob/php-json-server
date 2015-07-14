<?php


class JsonDataBaseTest extends PHPUnit_Framework_TestCase
{
    protected $fixture;

    protected function setUp()
    {
        require '/vendor/autoload.php';

        $this->fixture = new \JsonServer\JsonDataBase(file_get_contents(getcwd().'/tests/pluralDB.json'));
    }

    protected function tearDown()
    {
        $this->fixture = NULL;
    }

    public function testConstructorPassed()
    {
        new \JsonServer\JsonDataBase(file_get_contents(getcwd().'/tests/pluralDB.json'));
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage data should be JSON string
     */
    public function testConstructorFail()
    {
        new \JsonServer\JsonDataBase(['id' => 1, 'parent_id' => 2]);
    }


    public function testGetTable()
    {
        self::assertInstanceOf('JsonServer\JsonTable', $this->fixture->getTable('post'));
    }



}