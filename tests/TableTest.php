<?php


class JsonTableTest extends PHPUnit_Framework_TestCase
{
    protected $fixture;

    protected function setUp()
    {
        $this->fixture = new \JsonServer\Table([['id' => 3, 'parent_id' => 2], ['id' => 1, 'parent_id' => 2], ['id' => 5, 'parent_id' => 6]], "");
    }

    protected function tearDown()
    {
        $this->fixture = NULL;
    }

    public function testConstructorArray()
    {
        new \JsonServer\Table([['id' => 1, 'parent_id' => 2], ['id' => 3, 'parent_id' => 4], ['id' => 5, 'parent_id' => 6]], "");
    }

    public function testConstructorJsonRow()
    {
        new \JsonServer\Table([new \JsonServer\Row(['id' => 1, 'parent_id' => 2]), new \JsonServer\Row(['id' => 3, 'parent_id' => 4]), new \JsonServer\Row(['id' => 5, 'parent_id' => 6])], "");
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage data should be array or object of JsonRow
     */
    public function testConstructorFails()
    {
        new \JsonServer\Table(null, "");
    }

    public function testWhereExist()
    {
        self::assertEquals(get_class($this->fixture->where('id', 1)), 'JsonServer\Table', 'where with right id is working');
    }

    public function testWhereNotExist()
    {
        self::assertEquals(get_class($this->fixture->where('id', 777)), 'JsonServer\Table', 'where with wrong id is working');
    }

    public function testFindExist()
    {
        self::assertEquals(get_class($this->fixture->find(1)), 'JsonServer\Row', 'find with right id is working');
    }

    /**
     * @expectedException        OutOfRangeException
     * @expectedExceptionMessage there is no resource with id 777
     */
    public function testFindNotExist()
    {
        $this->fixture->find(777);
    }

    public function testGetNewIdIsOk()
    {
        self::assertEquals($this->fixture->getNewId(), 6, 'get new id is working');
    }

    public function testToArrayIsOk()
    {
        self::assertEquals($this->fixture->toArray(), [['id' => 1, 'parent_id' => 2], ['id' => 3, 'parent_id' => 2], ['id' => 5, 'parent_id' => 6]], 'toArray is working');
    }

    public function testSortDescIsOk()
    {
        $this->fixture->_sort('parent_id');
        $this->fixture->_order('desc');
        self::assertEquals($this->fixture->toArray(), [['id' => 5, 'parent_id' => 6], ['id' => 1, 'parent_id' => 2], ['id' => 3, 'parent_id' => 2]], 'sort desc is working');
    }

    public function testSortAscIsOk()
    {
        $this->fixture->_sort('parent_id');
        $this->fixture->_order('asc');
        self::assertEquals($this->fixture->toArray(), [['id' => 3, 'parent_id' => 2], ['id' => 1, 'parent_id' => 2], ['id' => 5, 'parent_id' => 6]], 'sort asc is working');
    }

    public function testLimitIsOk()
    {
        $this->fixture->_limit(2);
        self::assertEquals($this->fixture->toArray(), [['id' => 1, 'parent_id' => 2], ['id' => 3, 'parent_id' => 2]], 'limit is working');
    }

    public function testStartIsOk()
    {
        $this->fixture->_start(1);
        self::assertEquals($this->fixture->toArray(), [['id' => 3, 'parent_id' => 2], ['id' => 5, 'parent_id' => 6]], 'start is working');
    }

    public function testEndIsOk()
    {
        $this->fixture->_end(1);
        self::assertEquals($this->fixture->toArray(), [['id' => 1, 'parent_id' => 2]], 'end is working');
    }

    public function testFulltextIsOk()
    {
        $this->fixture = new \JsonServer\Table([['id' => 3, 'field' => 'some string'], ['id' => 1, 'field' => 'field with substring'], ['id' => 5, 'field' => 'another string']], "");
        $this->fixture = $this->fixture->_query('substring');
        self::assertEquals($this->fixture->toArray(), [['id' => 1, 'field' => 'field with substring']], 'fulltext end is working');
    }


    public function testInsertIsOk()
    {
        $before = $this->fixture->count();
        $this->fixture->insert(['parent_id' => 2]);
        $after = $this->fixture->count();
        self::assertEquals($before + 1, $after, 'insert is working');
    }

    public function testUpdateIsOk()
    {
        $oldVal = $this->fixture->find(1)->parent_id;
        $this->fixture->update(1, ['parent_id' => 3]);
        $newVal = $this->fixture->find(1)->parent_id;
        self::assertNotEquals($oldVal, $newVal, 'update is working');
    }

    /**
     * @expectedException        OutOfRangeException
     * @expectedExceptionMessage there is no resource with id 1
     */
    public function testDeleteIsOk()
    {
        $this->fixture->delete(1);
        $this->fixture->find(1);
    }

}