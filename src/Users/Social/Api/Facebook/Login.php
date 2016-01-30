<?php
namespace Extasy\Users\Social\Api\Facebook {
	use \UsersLogin;
	use \Extasy\Audit\Record;

	class Login extends \Extasy\Api\ApiOperation {
		const MethodName = 'users.facebook.login';
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

			$this->api           = \Extasy\Users\Social\FacebookApiFactory::getInstance();
			$userFacebookProfile = $this->api->getCurrentSession();
			$uid                 = $userFacebookProfile[ 'id' ];

			$user = \Extasy\Users\Columns\SocialNetworks::getByUID( $uid, 'facebook' );

			UsersLogin::testConfirmationCode( $user );
			UsersLogin::forceLogin( $user );

			$log = sprintf( 'Facebook login successfully finished. User ("%s", "%d") logged with uid ("%s" )',
							$user->login->getValue(),
							$user->id->getValue(),
							$userFacebookProfile[ 'id' ]
			);
			Record::add( __CLASS__, $log );
		}
	}
}