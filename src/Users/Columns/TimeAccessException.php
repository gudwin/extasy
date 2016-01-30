<?php
namespace Extasy\Users\Columns;


class TimeAccessException extends \Exception {
	public function __construct( $message = '', $code = 0, $previous = null) {
		\CMSLog::addMessage(__CLASS__, $message);
		parent::__construct( $message , $code, $previous = null );
	}
} 