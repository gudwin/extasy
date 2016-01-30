<?php
use \Faid\DBSimple;
class ACLCreateTest extends PHPUnit_Framework_TestCase {
	public function setUp() {
		include dirname( __FILE__ ) . '/import.php';
	}
	/**
	 * 
	 * @expectedException ACLException
	 */
	public function testCreateOnEmpty() {
		ACL::create('');
	}
	/**
	 * 
	 * @expectedException ACLException
	 */
	public function testCreateOnEmptySubObject() {
		ACL::create('test/');
	}
	public function testCreate() {
		ACL::create('test/xx/yy');
		$this->assertEquals(3,$this->getRowsCount());
		// Проверяем parentID у xx 
		$checkData = DBSimple::get(ACL_TABLE, 'id = 2');
		$this->assertEquals(1,$checkData['parentId']);
		ACL::create('test/xx/zz');
		$this->assertEquals(4,$this->getRowsCount());
		ACL::create('test/11/22');
		$this->assertEquals(6,$this->getRowsCount());
		ACL::create('x/y/z');
		$this->assertEquals(9,$this->getRowsCount());
		ACL::create('single_value');
		$this->assertEquals(10,$this->getRowsCount());
	}
	public function testCreateWithCheckValues() {
		ACL::create('test/test2');
		$result = DBSimple::get(ACL_TABLE,array('id' => 1));
		$this->assertEquals('test',$result['name']);
		$result = DBSimple::get(ACL_TABLE,array('id' => 2));
		$this->assertEquals('test2',$result['name']);
	}
	protected function getRowsCount() {
		return DBSimple::getRowsCount(ACL_TABLE);
	}
	
}