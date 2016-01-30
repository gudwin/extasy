<?php
namespace Extasy\tests\Api;
use Extasy\Api\ApiOperation;
use \Faid\DBSimple;
use \Extasy\tests\Helper as TestsHelper;
use \Extasy\Columns\Password as passwordColumn;

class ApiOperationTest extends \Extasy\tests\BaseTest {
	const defaultApiOperationName = 'Test.Hello';
	/**
	 * Any keyword which not equal to ApiOperationTest::defaultApiOperationName
	 */
	const WrongMethodName = 'UnknownExample';

	/**
	 * @expectedException \Extasy\Api\Exception
	 */
	public function testSetWrongCallback( ) {
		$method = new ApiOperation( );
		$method->setCallback( 'unknownFunc');

	}
	/**
	 * @expectedException \Extasy\Api\Exception
	 */
	public function testRequiredParamsMissed( ) {
		$method = new TestApiOperationWithParams();
		$method->exec( );
	}
	/**
	 * @group testRequiredParams
	 */
	public function testRequiredParams( ) {
		$fixture = array('msg' => 'Hello API');
		$method = new TestApiOperationWithParams();
		$method->setParamsData( $fixture );
		$result = $method->exec();

		$this->assertEquals( $fixture, $result );
	}
	public function testOptionalParams( ) {
		$fixture = array('msg' => 'Hello API','msg2' => 'Optional works');
		$method = new TestApiOperationWithParams();
		$method->setParamsData( $fixture );
		$result = $method->exec();

		$this->assertEquals( $fixture, $result );
	}
	public function testMatch( ) {
		$method = new TestApiOperation();
		$this->assertEquals( false, $method->match( self::WrongMethodName));
		$this->assertEquals( true, $method->match( self::defaultApiOperationName ));
	}

	/**
	 * @expectedException \ForbiddenException
	 */
	public function testCallApiOperationWithoutGrants( ) {
		$method = new TestApiWithACLOperation();
		$method->exec();
	}
	public function testCallApiOperationWithGrants( ) {
		TestsHelper::dbFixture( \UserAccount::getTableName(), array(
			array('login' => 'test', 'password' => passwordColumn::hash( 'testtest'))
		));
		$user = \UserAccount::getById( 1 );
		\ACL::create( TestApiWithACLOperation::RightName );
		\ACL::grant( TestApiWithACLOperation::RightName, $user->obj_rights->getEntity() );
		\UsersLogin::login('test','testtest');
		$method = new TestApiWithACLOperation();
		$this->assertTrue( $method->exec()) ;
	}
}