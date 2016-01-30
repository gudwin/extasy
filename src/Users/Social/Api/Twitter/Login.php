<?php
namespace Extasy\Users\Social\Api\Twitter {
	use \UsersLogin;
	use \Extasy\Audit\Record;

	class Login extends \Extasy\Api\ApiOperation {
		const MethodName = 'users.twitter.login';
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

			$this->api           = \Extasy\Users\Social\TwitterApiFactory::getInstance();
			$userTwitterProfile = $this->api->getCurrentSession();
			$uid                 = $userTwitterProfile[ 'id' ];

			$user = \Extasy\Users\Columns\SocialNetworks::getByUID( $uid, 'twitter' );

			UsersLogin::testConfirmationCode( $user );
			UsersLogin::forceLogin( $user );

			$log = sprintf( 'Twitter login successfully finished. User ("%s", "%d") logged with uid ("%s" )',
							$user->login->getValue(),
							$user->id->getValue(),
							$userTwitterProfile[ 'id' ]
			);
			Record::add( __CLASS__, $log );
		}
	}
}