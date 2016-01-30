<?php
use \Faid\DBSimple;
class ACLGrantTest extends PHPUnit_Framework_TestCase {
	public function setUp() {
		include dirname( __FILE__ ) . '/import.php';
	}
	/**
	 * 
	 * @expectedException ACLException
	 */
	public function testGrantOnEmpty() {
		ACL::create('test');
		ACL::grant('test','');
	}
	/**
	 * 
	 * @expectedException ACLException
	 */
	public function testGrantToUnknownAction() {
		ACL::create('test');
		ACL::grant('test2','e1');
	}
	public function testGrant() {
		ACL::create('test/test2');
		ACL::grant('test/test2','e1');
		$this->assertEquals(1,DBSimple::getRowsCount(ACL_GRANT_TABLE));
		$found = DBSimple::get(ACL_GRANT_TABLE, array(
			'actionId' => 2
		));
		$this->assertEquals('e1',$found['entity']);
	}
}