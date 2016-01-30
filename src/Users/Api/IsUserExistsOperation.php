<?php
namespace Extasy\Users\Api {
	use \UsersDBManager;

	class IsUserExistsOperation extends ApiOperation  {
		const MethodName = 'user.is.exists';

		protected $requiredParams = array('login');
		protected $requiredFiles = array();
		/**
		 *
		 */
		protected  function action( ) {
			$login = $this->getParam('login');
			try {
				$found = UsersDBManager::getByLogin( $login );
			} catch (\Exception $e ) {
				$found = null;
			}
			return !empty( $found );
		}
	}
}