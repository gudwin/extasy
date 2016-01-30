<?php
namespace Extasy\Users\Api {
	use \UsersLogin;

	class IsLoginedOperation extends \Extasy\Api\ApiOperation {
		const MethodName = 'user.isLogined';
		public function action( ) {
			return UsersLogin::isLogined();
		}
	}
}