<?php


class JsonTableTest extends PHPUnit_Framework_TestCase
{
    protected $fixture;

    protected function setUp()
    {

        $this->fixture = new \JsonServer\JsonTable([['id' => 1, 'parent_id' => 2],['id' => 3, 'parent_id' => 2],['id' => 5, 'parent_id' => 6]]);
    }

    protected function tearDown()
    {
        $this->fixture = NULL;
    }

    public function testConstructorArray()
    {
        new \JsonServer\JsonTable([['id' => 1, 'parent_id' => 2],['id' => 3, 'parent_id' => 4],['id' => 5, 'parent_id' => 6]]);
    }

    public function testConstructorJsonRow()
    {
        new \JsonServer\JsonTable([new \JsonServer\JsonRow(['id' => 1, 'parent_id' => 2]),new \JsonServer\JsonRow(['id' => 3, 'parent_id' => 4]),new \JsonServer\JsonRow(['id' => 5, 'parent_id' => 6])]);
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage data should be array or object of JsonRow
     */
    public function testConstructorFails()
    {
        new \JsonServer\JsonTable(null);
    }

    public function testWhereExist()
    {
        self::assertEquals(get_class($this->fixture->where('id', 1)), 'JsonServer\JsonTable');
    }

    public function testWhereNotExist()
    {
        self::assertEquals(get_class($this->fixture->where('id', 777)), 'JsonServer\JsonTable');
    }

    public function testFindExist()
    {
        self::assertEquals(get_class($this->fixture->find(1)), 'JsonServer\JsonRow');
    }

    public function testFindNotExist()
    {
        self::assertEquals($this->fixture->find(777), null);
    }

}