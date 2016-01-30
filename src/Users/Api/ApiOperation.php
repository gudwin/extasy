<?php
namespace Extasy\Users\Api {
	use \UsersLogin;
	use \UserAccount;
	use \ForbiddenException;
	class ApiOperation extends \Extasy\Api\ApiOperation {

		/**
		 * @var UserAccount
		 */
		protected $userSession = NULL;

		/**
		 * @return mixed|void
		 * @throws \ForbiddenException
		 */
		public function exec( ) {
			if ( UsersLogin::isLogined()) {
				$this->userSession = UsersLogin::getCurrentSession();
				return parent::exec();
			} else {
				throw new ForbiddenException('Need active session');
			}
		}
	}
}