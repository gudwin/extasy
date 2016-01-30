<?php
class ACLIsGrantedTest extends PHPUnit_Framework_TestCase {
	public function setUp() {
		include dirname( __FILE__ ) . '/import.php';
	}
	/**
	 * 
	 * @expectedException ACLException
	 */
	public function testOnUnknownPath() {
		ACL::isGranted('test_unknown', 'o1');	
	}
	
	public function testNormal() {
		$path = 'test/test/test3';
		ACL::create($path);
		ACL::grant($path, 'obj1');
		$this->assertEquals(true,ACL::isGranted($path, 'obj1'));
		
		
	}
	public function testDeleteGrant() {
		$path = 'test/test2';
		$entity = 'obj1';
		ACL::create($path);
		ACL::grant($path, $entity);
		$this->assertEquals(true,ACL::isGranted($path, $entity));
		ACL::unGrant($path, $entity);
		$this->assertEquals(false,ACL::isGranted($path, $entity));
	}

}