<?php
namespace Extasy\Users\Social\Api\Twitter {

	class Registration extends \Extasy\Api\ApiOperation {
		const MethodName = 'users.twitter.registration';
		protected $requiredParams = ['login','email','captcha'];

		protected function action() {
			\UsersRegistration_Registrate::testCaptcha( $this->getParam('captcha'));
			$this->api           = \Extasy\Users\Social\TwitterApiFactory::getInstance();
			$userTwitterProfile = $this->api->getCurrentSession();

			\UsersRegistration::signup(
							  $this->getParam('login'),
							  \Extasy\Columns\Password::generatePassword(),
							  $this->getParam('email'),
							  [
								  'social_networks' => [
									  'twitter' => $userTwitterProfile['id']
								  ]
							  ]
			);

			return true;
		}
	}
}