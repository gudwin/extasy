<?php
namespace Extasy\kernel {

	class Validator {
		protected $value = null;
		public function __construct( $value ) {
			$this->value = $value;
		}
		public function isValid() {
			return $this->test();
		}
		protected function test() {
			return false;
		}
	}
}