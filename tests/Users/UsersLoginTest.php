<?php
namespace Extasy\tests\Users;


use \Faid\DB;
use \Faid\DBSimple;
use \UsersLogin;
use \Extasy\tests\system_register\Restorator;

class UsersLoginTest extends UsersTest {

	static $remoteIP = false;

	public static function setUpBeforeClass() {
		self::$remoteIP = !empty( $_SERVER[ 'REMOTE_ADDR' ] ) ? $_SERVER[ 'REMOTE_ADDR' ] : '127.0.0.1';

	}


	public function setUp() {
		// clean up session
		$_SESSION                 = array();
		$_SERVER[ 'REMOTE_ADDR' ] = self::$remoteIP;
		parent::setUp();
	}

	public function tearDown() {
	}


	/**
	 * @expectedException \NotFoundException
	 */
	public function testLoginWithIncorrectData() {
		UsersLogin::login( self::UnknownLogin, self::Password );
	}

	/**
	 * @expectedException \Extasy\Users\login\LoginAttemptsException
	 */
	public function testLoginAttemptsPerHost() {
		self::configureLoginAttempts( 10, 0 );
		UsersLogin::login( self::Login, self::Password );

	}

	/**
	 * @expectedException \Extasy\Users\login\LoginAttemptsException
	 */
	public function testLoginPerSessionLimit() {
		self::configureLoginAttempts( 0, 10 );
		UsersLogin::login( self::Login, self::Password );
	}


	public function testLogin() {
		$this->assertFalse( UsersLogin::isLogined() );
		UsersLogin::login( self::Login, self::Password );
		$this->assertTrue( UsersLogin::isLogined() );
		$user = UsersLogin::getCurrentSession();
		$this->assertTrue( $user instanceof \UserAccount );
		$this->assertEquals( $user->id->getValue(), 1 );
		$this->assertEquals( $user->login->getValue(), self::Login );
	}

}