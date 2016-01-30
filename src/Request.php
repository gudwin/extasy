<?php
namespace Extasy;

use \Faid\Request\HttpRequest;

class Request extends HttpRequest {
	const LogName = 'Request.Injection';

	protected $patterns = array();

	public function __construct() {
		parent::__construct();
		$this->patterns = static::getPatterns();
	}

	public function testForInjections() {

		if ( \UsersLogin::isLogined() ) {
			if ( \CMSAuth::getInstance()->isAdmin( \UsersLogin::getCurrentSession())) {
				return ;
			}
		}

		foreach ( $this->data as $key => $value ) {
			self::validateParameter( $key, $value );
		}

		$this->validateParameter( 'Current page url', $this->uri() );

	}

	protected function validateParameter( $name, $value ) {
		if ( is_array( $value )) {
			foreach ($value as $key => $row ) {
				$this->validateParameter( sprintf('%s[%s]', $name, $key ), $row );
			}
		} else {
			foreach ( $this->patterns as $regExp ) {
				if ( preg_match( $regExp, $value ) ) {
					$short = sprintf( '`%s` matches injection pattern "%s" ',
									  htmlspecialchars( $name ),
									  htmlspecialchars( $regExp ) );
					$full  = sprintf( '<b>%s</b><br>Page URL: %s<br>Matching Pattern: %s<br>Request:<br>%s<br>',
									  htmlspecialchars( $name ),
									  htmlspecialchars( print_r( $this->uri(), true ) ),
									  htmlspecialchars( print_r( $regExp, true ) ),
									  htmlspecialchars( print_r( $this->data, true ) ) );
					\Extasy\Audit\Record::add( self::LogName, $short, $full );
				}
			}
		}

	}

	public static function getPatterns() {
		return array(
			// SQL meta characters
			'/(\%27)|(\\\')|(\-\-)|(\%23)|(\#)/ix',
			// Modified regex for detection of SQL meta-characters
			'/((\%3D)|(=))[^\n]*((\%27)|(\\\')|(\-\-)|(\%3B)|(;))/i',
			// Regex for typical SQL Injection attack
			'/\w*((\%27)|(\\\'))((\%6F)|o|(\%4F))((\%72)|r|(\%52))/ix',
			// Regex for simple CSS attack
			'/((\%3C)|<)((\%2F)|\/)*[a-z0-9\%]+((\%3E)|>)/ix',
			// Regex for "<img src" CSS attack
			'/((\%3C)|<)((\%69)|i|(\%49))((\%6D)|m|(\%4D))((\%67)|g|(\%47))[^\n]+((\%3E)|>)/i',
			// Paranoid regex for CSS attacks
			'/((\%3C)|<)[^\n]+((\%3E)|>)/i',

			// Dangegorous keywords:
			'/include/i',
			'/require_once/i',
			'/eval/i',
			'/exec/i',
			'/phpinfo/i',
		);
	}
}