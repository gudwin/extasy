<?php
namespace Extasy\Users\Social\Api\Vkontakte {

	class Registration extends \Extasy\Api\ApiOperation {
		const MethodName = 'users.vkontakte.registration';
		protected $requiredParams = ['login','email','captcha'];

		protected function action() {
			\UsersRegistration_Registrate::testCaptcha( $this->getParam('captcha'));
			$this->api           = \Extasy\Users\Social\VkontakteApiFactory::getInstance();
			$userVkontakteProfile = $this->api->getCurrentSession();

			\UsersRegistration::signup(
							  $this->getParam('login'),
							  \Extasy\Columns\Password::generatePassword(),
							  $this->getParam('email'),
							  [
								  'social_networks' => [
									  'vkontakte' => $userVkontakteProfile['id']
								  ]
							  ]
			);

			return true;
		}
	}
}