<?php

namespace Extasy\Users\Social\Odnoklassniki;

use Social\Api\Api;
use Social\Api\User;
use Social\SexType;
use Social\Auth\Token;
use Social\Util\HttpClient;

class ApiOd extends Api {

	const API_URL = 'http://api.odnoklassniki.ru/fb.do';
	protected $applicationKey = '';
	protected $applicationSecretKey = '';

	public function __construct( $applicationKey, $applicationSecretKey, Token $token ) {
		parent::__construct( $token );
		$this->applicationKey       = $applicationKey;
		$this->applicationSecretKey = $applicationSecretKey;

	}
	public static function getSignature( $data, $applicationSecret ) {
		if ( !isset( $data['access_token'])) {
			throw new \InvalidArgumentException('Access token not defined in signed request');
		}
		ksort( $data );
		$tmp = [];
		foreach ( $data as $key=>$value) {
			if ( $key == 'access_token') {
				continue;
			}
			$tmp[] = sprintf('%s=%s', $key, $value );
		}
		$tmp = implode('', $tmp );
		$signedString = md5( $data['access_token'].$applicationSecret );
		$signedString = strtolower( md5( $tmp.$signedString ) );
		return $signedString;

	}
	public static function request( $data,$applicationSecret ) {

		$data['sig'] = self::getSignature( $data, $applicationSecret );

		$response = HttpClient::exec('GET', self::API_URL, $data);
		$data = json_decode( $response, true );

		if ( isset( $data[ 'error' ] ) ) {
			throw new \RuntimeException( $data['error']);
		}
		return $data;

	}
	public function getProfile() {
		$token = $this->getToken();

		$parameters = array(
			'application_key' => $this->applicationKey,
			'method'          => 'users.getCurrentUser',
			'access_token'    => $token->getAccessToken(),
			'format' => 'json'
		);

		$data = self::request( $parameters, $this->applicationSecretKey );

		if ( !isset( $data[ 'uid' ] ) ) {
			$this->setError( 'wrong_response' );

			return null;
		}

		return $this->createUser( $data );
	}

	protected function createUser( $data ) {
		$user     = new User();
		$user->id = $data[ 'uid' ];
		if ( isset( $data[ 'first_name' ] ) ) {
			$user->firstName = $data[ 'first_name' ];
		}
		if ( isset( $data[ 'last_name' ] ) ) {
			$user->lastName = $data[ 'last_name' ];
		}
		$user->screenName = $data[ 'name' ];
		if ( isset( $data[ 'pic_1' ] ) ) {
			$user->photoUrl = $data[ 'pic_1' ];
		}

		if ( isset( $data[ 'pic_2' ] ) ) {
			$user->photoBigUrl = $data[ 'pic_2' ];
		}
		if ( isset( $data[ 'gender' ] ) ) {
			$user->sex = $data[ 'gender' ] == 'male' ? SexType::MALE : SexType::FEMALE;
		} else {
			$user->sex = SexType::NONE;
		}
		if ( isset( $data[ 'birthday' ] ) ) {
			$user->birthDate = $data[ 'birthday' ];
		}


		return $user;
	}
}