<?php
namespace Extasy\Users\Social;

use Facebook\FacebookSession;
use Facebook\FacebookRequest;
use Facebook\FacebookJavaScriptLoginHelper;
use Facebook\FacebookRequestException ;
use \Facebook\GraphUser;
use \Faid\Configure\Configure;

use \Extasy\Audit\Record;

class FacebookApi {
	const ConfigureKey = 'Facebook';
	const SessionKey = 'facebookToken';
	protected $token = null;
	protected $config = null;
	public function __construct( $accessToken = null ) {
		$this->config = Configure::read( self::ConfigureKey );
		FacebookSession::setDefaultApplication( $this->config['app_id'], $this->config['app_secret']);

		$this->token = $accessToken;
	}
	public static function cleanUpSession() {
		if ( isset( $_SESSION[self::SessionKey ])) {
			unset($_SESSION[self::SessionKey ]);
		}
	}
	public function getCurrentSession() {
		$helper = new FacebookJavaScriptLoginHelper();
		try {
			if ( !empty( $_SESSION[ self::SessionKey ])) {
				$accessToken = $_SESSION[ self::SessionKey ];
				$_SESSION[ self::SessionKey ] = null;
				$session = new \Facebook\FacebookSession( $accessToken);
			} else {
				$session = $helper->getSession();
				$accessToken = $session->getAccessToken();

				$_SESSION[ self::SessionKey ] = (string) $accessToken;
			}

		} catch(\Exception $ex) {
			Record::add( __CLASS__ , $ex->getMessage(), $ex);
			throw $ex;
		}
		if ($session) {
			try {

				$user_profile = (new FacebookRequest(
					$session, 'GET', '/me' )
				)->execute()->getGraphObject(GraphUser::className());

				return [
					'id' => $user_profile->getId(),
					'name' => $user_profile->getName()
				];

			} catch(FacebookRequestException $e) {
				$error = "Exception occured, code: " . $e->getCode() .
					" with message: " . $e->getMessage();;
				Record::add(__CLASS__, $error, $e );
				throw $e;
			}
		}
	}

} 