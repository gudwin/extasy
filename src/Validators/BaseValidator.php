<?php
namespace Extasy\Validators;


abstract class BaseValidator {

	public function isValid() {
		return $this->test();
	}
	abstract protected function test();
}

