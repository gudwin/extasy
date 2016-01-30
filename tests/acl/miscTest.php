<?php
use \Faid\DBSimple;
class ACLMiscTest extends PHPUnit_Framework_TestCase {
	public function setUp() {
		include dirname( __FILE__ ) . '/import.php';
	}
	/**
	 * 
	 */
	public function testRemoveEntity() {
		ACL::create('test');
		ACL::create('test2');
		ACL::create('test3');
		ACL::grant('test','obj1');
		ACL::grant('test2','obj2');
		ACL::grant('test3','obj1');
		ACL::removeEntity('obj1');
		$this->assertEquals(1,DBSimple::getRowsCount(ACL_GRANT_TABLE));
		$this->assertEquals(true,ACL::isGranted('test2', 'obj2'));
	}

	/**
	 * @group testSelectAllGrantsForEntity
	 */
	public function testSelectAllGrantsForEntity() {
		ACL::create('test');
		ACL::create('test2');
		ACL::create('test3');
		ACL::grant('test','obj1');
		ACL::grant('test2','obj2');
		ACL::grant('test3','obj1');
		$grantsList = ACL::selectAllGrantsForEntity('obj1');
		$this->assertEquals(2,sizeof($grantsList));
		$this->assertFalse( !empty($grantsList['test2'] ));
		$this->assertTrue( !empty($grantsList['test3'] ));
		$this->assertTrue( !empty($grantsList['test'] ));
	}
	public function testExport() {
		ACL::create('a/b/c');
		ACL::create('a/b/d');
		ACL::create('a/e');
	
		$exported = ACLMisc::export();
		$this->assertEquals(1,sizeof($exported));
		$this->assertEquals(2,sizeof($exported[0]['children']));
		$this->assertEquals(2,sizeof($exported[0]['children'][0]['children']));
		
	}
}