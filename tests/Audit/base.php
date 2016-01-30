<?php

namespace Extasy\tests\Audit;


class  base extends \Extasy\tests\BaseTest {
	public function setUp( ) {
		parent::setUp();
		include __DIR__ . DIRECTORY_SEPARATOR . 'fixtures.php';
	}
	public function tearDown( ) {
		if ( \UsersLogin::isLogined() ){
			\UsersLogin::logout();
		}
	}
}