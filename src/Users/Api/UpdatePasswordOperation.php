<?php
namespace Extasy\Users\Api {
	use \Extasy\Columns\Password as passwordColumn;
	use \Extasy\Users\UsersModule;

	class UpdatePasswordOperation extends ApiOperation  {
		const MethodName = 'user.updatePassword';

		protected $requiredParams = array('password','old_password');
		/**
		 *
		 */
		protected  function action( ) {
			$oldPassword = $this->getParam('old_password');
			$oldPasswordHash = PasswordColumn::hash( $oldPassword );

			$password = $this->getParam('password');
			if ($this->userSession->password->getValue() != $oldPasswordHash)
			{
				throw new \ForbiddenException('Wrong password');
			}
			$this->userSession->password->setValue( $password );
			$this->userSession->update();
			$this->sendNotificationEmail($password);
			return array(
				'status' => true
			);
		}
		protected function sendNotificationEmail($password) {
			$data = [
				'email' => $this->userSession->email->getValue(),
				'login' => $this->userSession->login->getValue(),
				'password' => $password
			];
			UsersModule::sendEmail( $data, \UserAccount::UpdatePasswordConfigName );
		}
	}
}