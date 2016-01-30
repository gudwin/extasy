<?php

namespace Extasy\tests\Audit;

use \Extasy\tests\Helper as TestsHelper;
use Extasy\Audit\Api\ApiOperation;
use \Extasy\Columns\Password as passwordColumn;

class ApiTest extends base {
	/**
	 * @expectedException \ForbiddenException
	 */
	public function testPermissionRequired() {
		TestsHelper::dbFixture(
			USERS_TABLE, array(
							  array('login' => 'login', 'password' => passwordColumn::hash('testtest')),
						 )
		);
		TestsHelper::dbFixture( \ACL_GRANT_TABLE, array());
		//
		\UsersLogin::login('login', 'testtest');

		$operation = new ApiOperation();
		$operation->exec();
	}

	public function testWithRequiredRights() {
		$operation = new ApiOperation();
		$operation->exec();
	}

}