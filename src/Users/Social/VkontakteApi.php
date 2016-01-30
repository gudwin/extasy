<?php
namespace Extasy\Users\Social;


class VkontakteApi {
	const ConfigureKey = 'Vkontakte';
	const SessionKey   = 'vkontakteToken';
	protected $token = null;
	protected $config = null;

	public function __construct( $accessToken = null ) {
		$this->config                   = \Faid\Configure\Configure::read( self::ConfigureKey );
		$this->config[ 'redirect_url' ] = sprintf( 'http://%s/oauth/vkontakte/', \Extasy\CMS::getMainDomain() );
		$this->token                    = $accessToken;
	}

	public static function cleanUpSession() {
		if ( isset( $_SESSION[ self::SessionKey ] ) ) {
			unset( $_SESSION[ self::SessionKey ] );
		}
	}

	public function getAuthUrl() {
		$auth = new \Social\Auth\AuthVk( $this->config[ 'app_id' ], $this->config[ 'app_secret' ], '' );
		$url  = $auth->getAuthorizeUrl( $this->config[ 'redirect_url' ] );;
		return $url;
	}

	public function authCallback() {
		$auth  = new \Social\Auth\AuthVk( $this->config[ 'app_id' ], $this->config[ 'app_secret' ], '' );
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

	public function getCurrentSession() {
		if ( empty( $_SESSION[ self::SessionKey ] ) ) {
			throw new \ForbiddenException( 'Vkontakte token not found' );
		}
		$session = $_SESSION[ self::SessionKey ];
		$token   = new \Social\Auth\Token( $session[ 'type' ], $session[ 'accessToken' ], $session[ 'identifier' ], $session[ 'expires' ] );
		$api     = new \Social\Api\ApiVk( $token);
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