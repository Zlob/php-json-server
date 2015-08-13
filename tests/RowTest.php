<?php


class RowTest extends PHPUnit_Framework_TestCase
{
    protected $fixture;

    protected function setUp()
    {
        $this->fixture = new \JsonServer\Row(['id' => 0, 'field1' => 1, 'field2' => 'some string']);
    }

    protected function tearDown()
    {
        $this->fixture = NULL;
    }

    public function testGetPassed()
    {
        static::assertEquals($this->fixture->field1, 1, 'get field from row is working');
    }

    public function testSetPassed()
    {
        $this->fixture->field1 = "new value";
        static::assertEquals($this->fixture->field1, 'new value', 'get field from row is working');
    }

    public function testSetFields()
    {
        $data = ['id' => 77, 'field1' => 11, 'field2' => 22];
        $this->fixture->setData($data);
        $dataExpext = ['id' => 0, 'field1' => 11, 'field2' => 22];
        static::assertEquals($this->fixture->toArray(), $dataExpext, 'mass assignment trough setData is working');
    }

    public function testSortingFields()
    {
        $data = ['field2' => 22, 'field1' => 11, 'id' => 77];
        $this->fixture->setData($data);
        $dataExpext = ['id' => 0, 'field1' => 11, 'field2' => 22];
        static::assertEquals($this->fixture->toArray(), $dataExpext, 'fields sorting correctly');
    }

    /**
     * @expectedException        OutOfRangeException
     * @expectedExceptionMessage there is no key unknownField in row
     */
    public function testGetFails()
    {
        $this->fixture->unknownField;
    }

    public function testToArray()
    {
        static::assertArrayHasKey('field1', $this->fixture->toArray(), 'toArray is working');
    }


    public function testSearch()
    {
        static::assertTrue($this->fixture->search('string'));
        static::assertFalse($this->fixture->search('fail'));
    }


    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage wrong param should be array
     */
    public function testConstructorFails()
    {
        new \JsonServer\Row('wrong param');
    }

}