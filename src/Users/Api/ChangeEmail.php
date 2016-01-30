<?php
namespace Extasy\Users\Api {

	class ChangeEmail extends ApiOperation  {
		const MethodName = 'user.ChangeEmail';

		protected $requiredParams = array('email');
		/**
		 *
		 */
		protected  function action( ) {
			$this->userSession->updateEmail( $this->getParam('email'));
			return array(
				'status' => true
			);
		}
	}
}