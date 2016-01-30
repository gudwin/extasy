<?php
namespace Extasy\Users\Social\Api\Vkontakte {
	use \UsersLogin;
	use \Extasy\Audit\Record;

	class Login extends \Extasy\Api\ApiOperation {
		const MethodName = 'users.vkontakte.login';
		protected $api = null;

		protected function action() {
			try {
				UsersLogin::testLoginAttempts();
			}
			catch ( \Exception $e ) {
				Record::add( __CLASS__, $e->getMessage(), $e );
				throw $e;
			}

			//

			$this->api           = \Extasy\Users\Social\VkontakteApiFactory::getInstance();
			$userVkontakteProfile = $this->api->getCurrentSession();
			$uid                 = $userVkontakteProfile[ 'id' ];

			$user = \Extasy\Users\Columns\SocialNetworks::getByUID( $uid, 'vkontakte' );

			UsersLogin::testConfirmationCode( $user );
			UsersLogin::forceLogin( $user );

			$log = sprintf( 'Vkontakte login successfully finished. User ("%s", "%d") logged with uid ("%s" )',
							$user->login->getValue(),
							$user->id->getValue(),
							$userVkontakteProfile[ 'id' ]
			);
			Record::add( __CLASS__, $log );
		}
	}
}