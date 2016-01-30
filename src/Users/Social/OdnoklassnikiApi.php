<?php
namespace Extasy\Users\Social;

use Extasy\Users\Social\Odnoklassniki\ApiOd;
use Extasy\Users\Social\Odnoklassniki\AuthOd;
use Social\Auth\Token;
class OdnoklassnikiApi {
	const ConfigureKey = 'Odnoklassniki';
	const SessionKey   = 'odnoklassnikiToken';
	protected $token = null;
	protected $config = null;

	public function __construct( $accessToken = null ) {
		$this->config                   = \Faid\Configure\Configure::read( self::ConfigureKey );
		$this->config[ 'redirect_url' ] = sprintf( 'http://%s/oauth/odnoklassniki/', \Extasy\CMS::getMainDomain() );
		$this->token                    = $accessToken;
	}

	public static function cleanUpSession() {
		if ( isset( $_SESSION[ self::SessionKey ] ) ) {
			unset( $_SESSION[ self::SessionKey ] );
		}
	}

	public function getAuthUrl() {
		$auth = $this->authFactory();
		$url  = $auth->getAuthorizeUrl( $this->config[ 'redirect_url' ] );;
		return $url;
	}

	public function authCallback() {
		$auth  = $this->authFactory();
		$token = $auth->authenticate( $_REQUEST, $this->config[ 'redirect_url' ] );
		if ( $token == null ) {
			throw new \RuntimeException( $auth->getError() );
		}
		$_SESSION[ self::SessionKey ] = [
			'type' => $token->getType(),
			'expires' => $token->getExpiresIn(),
			'accessToken' => $token->getAccessToken(),
			'identifier'  => $token->getIdentifier(),

		];
	}

	/**
	 * @return \Social\Auth\AuthTwitter
	 */
	protected function authFactory() {
		$api = new AuthOd( $this->config[ 'app_id' ], $this->config[ 'app_secret' ], '', $this->config[ 'app_public' ] );
		return $api;
	}

	public function getCurrentSession() {
		if ( empty( $_SESSION[ self::SessionKey ] ) ) {
			throw new \ForbiddenException( 'Odnoklassniki token not found' );
		}
		$session = $_SESSION[ self::SessionKey ];
		$token   = new Token( $session[ 'type' ], $session[ 'accessToken' ], $session[ 'identifier' ], $session[ 'expires' ] );

		$api     = new ApiOd( $this->config[ 'app_public' ], $this->config[ 'app_secret' ], $token );
		$user    = $api->getProfile();
		if ( empty( $user ) ) {
			throw new \RuntimeException( $api->getError() );
		}
		return [
			'id'   => $user->id,
			'name' => $user->firstName . ' ' . $user->lastName
		];
	}

} 