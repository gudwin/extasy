<?php

namespace Extasy\Users\Social\Odnoklassniki;

use Social\Auth\OAuth2;
use Social\Type;
use Social\Auth\Token;

class AuthOd extends OAuth2 {
	/**
	 * According documentation
	 * @url http://apiok.ru/wiki/pages/viewpage.action?pageId=81822109
	 */
	const TOKEN_EXPIRES_IN = 1800;
	protected $publicKey = '';

	public function __construct( $id, $secret, $scope, $publicKey ) {
		$this->publicKey = $publicKey;
		parent::__construct( $id, $secret, $scope );
	}

	protected function createToken( $token ) {
		return new Token( $this->getType(), $token[ 'access_token' ], $token[ 'user_id' ], self::TOKEN_EXPIRES_IN );
	}

	public function getType() {
		return 'Odnoklassiki';
	}

	protected function getAuthUrl() {
		return 'http://www.odnoklassniki.ru/oauth/authorize';
	}

	protected function getTokenUrl() {
		return 'https://api.odnoklassniki.ru/oauth/token.do';
	}

	protected function execPost( $url, $data = array() ) {
		return \Social\Util\HttpClient::exec( 'POST', $url, http_build_query( $data, null, '&' ) );
	}

	protected function parseToken( $token ) {
		if ( !is_array( $token )) {
			throw new \RuntimeException('Not an array. $token - '. print_r( $token, true ));
		}
		$data     = [
			'application_key' => $this->publicKey,
			'method'          => 'users.getCurrentUser',
			'access_token'    => $token[ 'access_token' ],
			'format'          => 'json'
		];
		$response = ApiOd::request( $data, $this->getSecret() );

		if ( isset( $response[ 'error' ] ) ) {
			throw new \RuntimeException( $response[ 'error' ] );
		}
		$token[ 'user_id' ] = $response[ 'uid' ];
		$token[ 'expires_in' ] = static::TOKEN_EXPIRES_IN;
		return $token;
	}

}