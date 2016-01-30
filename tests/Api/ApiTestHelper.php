<?php
namespace Extasy\tests\Api;

use \Extasy\CMS;

class ApiTestHelper {
	protected $cookieFile = null;
	protected $debug = false;
	public function __construct( $debug = false ) {
		$this->debug = $debug;
		$this->cookieFile = FILE_PATH . uniqid( __CLASS__ );
		file_put_contents( $this->cookieFile, $this->getInitialCookies() );
	}

	public function __destruct() {
		if ( file_exists( $this->cookieFile ) && is_file( $this->cookieFile ) ) {
			unlink( $this->cookieFile );
		}
	}

	public function auth( $login, $password ) {
		$data   = array(
			'login'    => $login,
			'password' => $password,
			'cms_auth' => true,
		);
		$ch     = $this->getCurlInstance( $data, $this->getLoginUrl() );
		$result = $this->execRequest( $ch );
		if ( !empty( $this->debug )) {
            print_r( $result->response );
		}
		//
		if ( !empty( $result->error ) ) {
			throw new \Exception( $result->error );
		}
	}

	public function makeRequest( $data ) {
		$ch = $this->getCurlInstance( $data );

		$result = $this->execRequest( $ch );

		return $result;
	}

	public function json( $data ) {
		$response = $this->makeRequest( $data );
		if ( !empty( $this->debug )) {
			print_r( $response->response );
		}
		return json_decode( $response->response, true );
	}

	protected function execRequest( $ch ) {
		//
		$result           = new \StdClass();
		$result->response = curl_exec( $ch );

		$result->code     = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
		$result->meta     = curl_getinfo( $ch );
		$result->error    = '';

		$curl_error = ( $result->code > 0 ) ? null : sprintf( 'Curl error %s (%s)',
															  curl_error( $ch ),
															  curl_errno( $ch ) );
		curl_close( $ch );

		if ( $curl_error ) {
			$result->error = $curl_error;
		}
		return $result;
	}

	protected function getLoginUrl() {
		$domain        = \Faid\Configure\Configure::read( 'MainDomain' );
		$dashboardRoot = \Extasy\CMS::getInstance()->getDashboardWWWRoot();

		return sprintf( 'http://%s%s', $domain, $dashboardRoot );
	}

	protected function getApiUrl() {
		$domain = \Faid\Configure\Configure::read( 'MainDomain' );
		return sprintf( 'http://%s/api/', $domain );
	}

	public function getCurlInstance( $data, $url = '' ) {
		$ch = curl_init( !empty( $url ) ? $url : $this->getApiUrl() );

		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 2 );
		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, false );
		curl_setopt( $ch, CURLOPT_MAXREDIRS, 1 );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );


		curl_setopt( $ch, CURLOPT_POST, true );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $this->array2CurlPostFields($data) );
		curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 10 );
		curl_setopt( $ch, CURLOPT_TIMEOUT, 30 );

		curl_setopt( $ch, CURLOPT_COOKIEJAR, $this->cookieFile );
		curl_setopt( $ch, CURLOPT_COOKIEFILE, $this->cookieFile );
		return $ch;
	}
	protected function array2CurlPostFields( $data,$prefix = '' ) {
		$result = array();
		foreach ( $data as $key=>$row ) {

			$fieldKey = sprintf('%s[%s]', $prefix, $key);
			$fieldKey = !empty( $prefix ) ? $fieldKey : $key;

			if ( is_array( $row )) {
				$child = self::array2CurlPostFields( $row, $fieldKey  );
				$result = array_merge($result, $child );
			} else {
				$result[ $fieldKey ] = $row;
			}
		}
		return $result;
	}
	/**
	 *
	 */
	protected function getInitialCookies() {
		return sprintf( ".%s\tTRUE\t/\tFALSE\t%d\t%s\t%s",
						\Faid\Configure\Configure::read( 'MainDomain' ),
						time() + 3600,
						CMS::UnitTestCookieName,
						CMS::getUnitTestCookie()
		);
	}
}