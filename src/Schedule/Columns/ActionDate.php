<?php
namespace Extasy\Schedule\Columns;


class ActionDate extends \Extasy\Columns\Datetime {
	public function setTime( $time ) {
		$value = strtotime( $time );
		if ( empty( $value ) ) {
			$value = time();
		}
		$this->aValue = date( 'Y-m-d H:i:s', $value );
	}
} 