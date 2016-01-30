<?php

namespace Extasy\Validators;


class ReadableFile extends BaseValidator {
	protected $path;
	public function __construct( $path ) {
		$this->path = $path;
	}
	protected function test() {
		return is_readable( $this->path ) && is_file( $this->path );
	}
} 