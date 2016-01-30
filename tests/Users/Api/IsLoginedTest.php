<?php

namespace Extasy\tests\Users\Api;

use \Extasy\Users\Api\IsLoginedOperation;
use \UsersLogin;

class IsLoginedTest extends \Extasy\tests\Users\UsersTest {
	public function testNotLoggedIn() {
		$api = new IsLoginedOperation();
		$this->assertFalse( $api->exec());
	}
	public function testLoggedIn() {
		UsersLogin::login( self::Login, self::Password );
		$api = new IsLoginedOperation();
		$this->assertTrue( $api->exec() );
	}
} 