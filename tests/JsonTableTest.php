<?php


class JsonTableTest extends PHPUnit_Framework_TestCase
{
    protected $fixture;

    protected function setUp()
    {
        $this->fixture = new \JsonServer\Table([['id' => 3, 'parent_id' => 2],['id' => 1, 'parent_id' => 2],['id' => 5, 'parent_id' => 6]], "");
    }

    protected function tearDown()
    {
        $this->fixture = NULL;
    }

    public function testConstructorArray()
    {
        new \JsonServer\Table([['id' => 1, 'parent_id' => 2],['id' => 3, 'parent_id' => 4],['id' => 5, 'parent_id' => 6]], "");
    }

    public function testConstructorJsonRow()
    {
        new \JsonServer\Table([new \JsonServer\Row(['id' => 1, 'parent_id' => 2]),new \JsonServer\Row(['id' => 3, 'parent_id' => 4]),new \JsonServer\Row(['id' => 5, 'parent_id' => 6])], "");
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
        self::assertEquals(get_class($this->fixture->where('id', 777)), 'JsonServer\Table', 'where with wrong id is working' );
    }

    public function testFindExist()
    {
        self::assertEquals(get_class($this->fixture->find(1)), 'JsonServer\Row', 'find with right id is working');
    }

    public function testFindNotExist()
    {
        self::assertEquals($this->fixture->find(777), null, 'find with wrong id is working');
    }

    public function testFilterByParentOk()
    {
        self::assertEquals(get_class($this->fixture->filterByParent(['table' => 'parents', 'id' => 2])), 'JsonServer\Table', 'filter by parent is working');
    }

    public function testGetNewIdIsOk()
    {
        self::assertEquals($this->fixture->getNewId(), 6, 'get new id is working');
    }

    public function testToArrayIsOk()
    {
        self::assertEquals($this->fixture->toArray(), [['id' => 1, 'parent_id' => 2],['id' => 3, 'parent_id' => 2],['id' => 5, 'parent_id' => 6]], 'toArray is working');
    }

    public function testInsertIsOk()
    {
        $before = $this->fixture->count();
        $this->fixture->insert(['parent_id' => 2]);
        $after =  $this->fixture->count();
        self::assertEquals($before + 1, $after, 'insert is working');
    }

    public function testUpdateIsOk()
    {
        $oldVal = $this->fixture->find(1)->parent_id;
        $this->fixture->update(1,['parent_id' => 3]);
        $newVal = $this->fixture->find(1)->parent_id;
        self::assertNotEquals($oldVal, $newVal, 'update is working');
    }

    public function testDeleteIsOk()
    {
        $this->fixture->delete(1);
        $newVal = $this->fixture->find(1);
        self::assertEquals($newVal, null, 'delete is working');
    }

}