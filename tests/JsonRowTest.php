<?php


class JsonRowTest extends PHPUnit_Framework_TestCase
{
    protected $fixture;

    protected function setUp()
    {
        $this->fixture = new \JsonServer\JsonRow(['id'=>0, 'field1' => 1, 'field2' => 2]);
    }

    protected function tearDown()
    {
        $this->fixture = NULL;
    }

    public function testGetPassed()
    {
        static::assertEquals($this->fixture->field1, 1);
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
        static::assertArrayHasKey('field1', $this->fixture->toArray());
    }

    public function testConstructor()
    {
        new \JsonServer\JsonRow(['id'=>0, 'field1' => 1, 'field2' => 2]);
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage wrong param should be array
     */
    public function testConstructorFails()
    {
        new \JsonServer\JsonRow('wrong param');
    }

}