<?php

namespace Extasy\tests\Users\Api;

use \Extasy\Users\Api\Login;
use \Extasy\users\Api\IsLoginedOperation;
class LoginTest extends \Extasy\tests\Users\UsersTest {
	/**
	 * @expectedException \NotFoundException
	 */
	public function testUnknownUser() {
		$api = new Login(array('login' => self::UnknownLogin,'password' => self::Password));
		$api->exec();
	}

	public function testLogin() {
		$isLogined = new IsLoginedOperation();
		$this->assertFalse( $isLogined->exec());

		$api = new Login(array('login' => self::Login, 'password' => self::Password ));
		$api->exec();

		$this->assertTrue( $isLogined->exec());
	}

}
