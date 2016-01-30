<?php

namespace Extasy\Validators;


class Date extends BaseValidator {
	protected $date = null;
	public function __construct( $date ) {
		$this->date = $date;
	}
	protected function test( ) {
		return (bool) preg_match( '/^[0-9]{4}\-[0-9]{1,2}\-[0-9]{1,2}$/', $this->date );
	}
} 