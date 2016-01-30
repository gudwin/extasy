<?php

namespace Extasy\tests\Users\Api;

use \Extasy\Users\Api\IsLoginedOperation;
use \Extasy\Users\Api\Logout;

class LogoutTest extends \Extasy\tests\Users\UsersTest  {
	public function testLogout( ) {
		\UsersLogin::login( self::Login, self::Password );
		$this->AssertTrue( \UsersLogin::isLogined()  );

		$api = new Logout();
		$api->exec();
		$this->AssertFalse( \UsersLogin::isLogined()  );
		//
	}
} 