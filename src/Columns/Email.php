<?php

namespace Extasy\Columns;

use \Extasy\Validators\Email as EmailValidator;
class Email extends Input {
	public function setValue( $newValue ) {
		if ( !empty( $newValue ) ) {
			$validator = new EmailValidator( $newValue );
			if ( !$validator->isValid() ) {
				throw new \InvalidArgumentException( sprintf( 'Not an email: "%s"', $newValue ));
			}
		}
		return parent::setValue( $newValue );
	}
} 