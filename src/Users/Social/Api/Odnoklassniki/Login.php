<?php
namespace Extasy\Users\Social\Api\Odnoklassniki {
	use \UsersLogin;
	use \Extasy\Audit\Record;

	class Login extends \Extasy\Api\ApiOperation {
		const MethodName = 'users.odnoklassniki.login';
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

			$this->api           = \Extasy\Users\Social\OdnoklassnikiApiFactory::getInstance();
			$userOdnoklassnikiProfile = $this->api->getCurrentSession();
			$uid                 = $userOdnoklassnikiProfile[ 'id' ];

			$user = \Extasy\Users\Columns\SocialNetworks::getByUID( $uid, 'odnoklassniki' );

			UsersLogin::testConfirmationCode( $user );
			UsersLogin::forceLogin( $user );

			$log = sprintf( 'Odnoklassniki login successfully finished. User ("%s", "%d") logged with uid ("%s" )',
							$user->login->getValue(),
							$user->id->getValue(),
							$userOdnoklassnikiProfile[ 'id' ]
			);
			Record::add( __CLASS__, $log );

		}
	}
}