<?php
use \Faid\DBSimple;
class ACLDeleteTest extends PHPUnit_Framework_TestCase {
	public function setUp() {
		include dirname( __FILE__ ) . '/import.php';
	}
	/**
	 * 
	 * @expectedException ACLException
	 */
	public function testDeleteUnknownPath() {
		ACL::create('test/test2');
		ACL::remove('test/test3');
	}
	/**
	 * 
	 * @expectedException ACLException
	 */
	public function testDeleteEmptyPath() {
		ACL::remove('');
	}
	
	public function testDelete() {
		ACL::create('test/test2');
		ACL::remove('test/test2');
		$this->assertEquals(1,DBSimple::getRowsCount(ACL_TABLE));
	}
	public function testDeleteWithSubobjects() {
		ACL::create('test/test2/test3');
		ACL::create('test/test4');
		ACL::create('test2');
		ACL::remove('test');
		
		$this->assertEquals(1,DBSimple::getRowsCount(ACL_TABLE));
	}
	public function testDeleteAndCheckEntityDeletion() {
		ACL::create('test/test2/test3');
		ACL::grant('test/test2/test3', 'o1');
		ACL::grant('test', 'o2');
		ACL::remove('test/test2');
		
		$this->assertEquals(1,DBSimple::getRowsCount(ACL_GRANT_TABLE));
		// Проверяем, что удалились именно нужные данные
		$this->assertEquals(true,ACL::isGranted('test', 'o2'));
	}
	
}