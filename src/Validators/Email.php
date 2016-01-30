<?php

namespace Extasy\Validators;


class Email extends BaseValidator {

	const EmailRegExp = '/.+@.+\..+/i';

	protected $email = null;
	public function __construct( $email ) {
		$this->email = $email;
	}
	protected function test() {
		return (bool)preg_match( self::EmailRegExp, $this->email );
	}
} 