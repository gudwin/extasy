<?php
namespace Extasy\tests;

use \UsersLogin;

abstract class BaseTest extends \PHPUnit_Framework_TestCase {
	public function setUp( ) {
		Helper::dbFixture( UsersLogin::LoginAttemptsTable, array() );
        $_SESSION[ UsersLogin::LogSessionKey ] = 0;
	}

	public function tearDown() {
		if ( UsersLogin::isLogined() ) {
			UsersLogin::logout();
		}
		Helper::cleanACL();
		Helper::cleanSchedule();
		Helper::cleanAudit();

		parent::tearDown();
	}
}