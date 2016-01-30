<?php
namespace Extasy\Users\Social\Api\Facebook {

	class Registration extends \Extasy\Api\ApiOperation {
		const MethodName = 'users.facebook.registration';
		protected $requiredParams = ['login','email','captcha'];

		protected function action() {
			\UsersRegistration_Registrate::testCaptcha( $this->getParam('captcha'));
			$this->api           = \Extasy\Users\Social\FacebookApiFactory::getInstance();
			$userFacebookProfile = $this->api->getCurrentSession();

			\UsersRegistration::signup(
				$this->getParam('login'),
				\Extasy\Columns\Password::generatePassword(),
				$this->getParam('email'),
				[
					'social_networks' => [
						'facebook' => $userFacebookProfile['id']
					]
				]
			);

			return true;
		}
	}
}