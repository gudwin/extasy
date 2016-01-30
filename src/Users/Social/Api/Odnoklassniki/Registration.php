<?php
namespace Extasy\Users\Social\Api\Odnoklassniki {

	class Registration extends \Extasy\Api\ApiOperation {
		const MethodName = 'users.odnoklassniki.registration';
		protected $requiredParams = ['login','email','captcha'];

		protected function action() {
			\UsersRegistration_Registrate::testCaptcha( $this->getParam('captcha'),'kcaptcha');
			$this->api           = \Extasy\Users\Social\OdnoklassnikiApiFactory::getInstance();
			$userOdProfile = $this->api->getCurrentSession();

			\UsersRegistration::signup(
							  $this->getParam('login'),
							  \Extasy\Columns\Password::generatePassword(),
							  $this->getParam('email'),
							  [
								  'social_networks' => [
									  'odnoklassniki' => $userOdProfile['id']
								  ]
							  ]
			);

			return true;
		}
	}
}