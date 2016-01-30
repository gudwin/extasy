<?php

namespace Extasy\Validators;


class Datetime extends BaseValidator {
	protected $datetime = null;
	public function __construct( $datetime ) {
		$this->datetime = $datetime;
	}
	protected function test( ) {
		return (bool) preg_match('/[0-9]{4}\-[0-9]{1,2}\-[0-9]{1,2} [0-9]{2}\:[0-9]{1,2}\:[0-9]{1,2}/', $this->datetime );
	}
} 