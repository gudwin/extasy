<?php


namespace Extasy\Users\Api;


class Logout extends \Extasy\Users\Api\ApiOperation {
	protected function action() {
		if ( \UsersLogin::isLogined() ) {
			\UsersLogin::logout();
		}
	}
} 