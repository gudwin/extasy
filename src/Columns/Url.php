<?php

namespace Extasy\Columns;


class Url extends Input{
	public function setValue( $newValue ) {
		if ( !preg_match( '#^[^\:]+\:\/\/#', $newValue)) {
			$newValue = 'http://' . $newValue;
		}
		return parent::setValue( $newValue );
	}
} 