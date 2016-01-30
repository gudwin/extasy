<?php
namespace Extasy\tests\Menu;
use \UsersLogin;
abstract class BaseTest extends \PHPUnit_Framework_TestCase {
	public function setUp( ) {
		parent::setUp();
		MenuFixtures::run();
	}
	public function tearDown( ) {
		parent::tearDown( );
		if (UsersLogin::isLogined()) {
			UsersLogin::logout();
		}
	}
}