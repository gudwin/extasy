<?php
namespace Extasy\Model;


class ParseDataException {
	protected $column = null;

	public static function factory( $column, $message = 'Column method ' ) {
		$result = new ParseDataException();

	}
} 