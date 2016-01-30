<?php

namespace Extasy\Validators;


class WritableFile extends BaseValidator {
	protected $path = null;
	public function __construct( $path ) {
		$this->path =  $path;
	}
	protected function test( ) {
		if ( is_dir( $this->path )) {
			return false;
		}
		if ( file_exists( $this->path)) {
			return is_writable( $this->path );
		} else {
			$dirPath = dirname( $this->path );
			return file_exists( $dirPath ) && is_dir( $dirPath ) && is_writable( $dirPath );
		}
	}
} 