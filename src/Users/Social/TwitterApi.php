<?php
namespace Extasy\Users\Social;

class TwitterApi {
	const ConfigureKey = 'Twitter';
	const SessionKey   = 'twitterToken';
	protected $token = null;
	protected $config = null;

	public function __construct( $accessToken = null ) {
		$this->config                   = \Faid\Configure\Configure::read( self::ConfigureKey );
		$this->config[ 'redirect_url' ] = sprintf( 'http://%s/oauth/twitter/', \Extasy\CMS::getMainDomain() );
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
			'type'        => $token->getType(),
			'accessToken' => $token->getAccessToken(),
			'identifier'  => $token->getIdentifier(),
			'expires'     => $token->getExpiresIn()

		];
	}

	/**
	 * @return \Social\Auth\AuthTwitter
	 */
	protected function authFactory() {
		return new \Social\Auth\AuthTwitter( $this->config[ 'app_id' ], $this->config[ 'app_secret' ], '' );
	}

	public function getCurrentSession() {
		if ( empty( $_SESSION[ self::SessionKey ] ) ) {
			throw new \ForbiddenException( 'Twitter token not found' );
		}
		$session = $_SESSION[ self::SessionKey ];
		$token   = new \Social\Auth\Token( $session[ 'type' ], $session[ 'accessToken' ], $session[ 'identifier' ], $session[ 'expires' ] );
		$api  = new \Social\Api\ApiTwitter( $this->config[ 'app_id' ], $this->config[ 'app_secret' ], $token );
		$user = $api->getProfile();
		if ( empty( $user ) ) {
			throw new \RuntimeException( $api->getError() );
		}
		return [
			'id'   => $user->id,
			'name' => $user->firstName . ' ' . $user->lastName
		];
	}

} 