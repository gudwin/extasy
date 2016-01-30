<?php

namespace Extasy\tests\Validators;

use \Extasy\Validators\Date;
class DateTest extends \Extasy\tests\BaseTest{
	public function testInvalidFormats( ) {
		$formats = array(
			'2001=01=01',
			'not an format',
			'',
			'201-01'
		);
		foreach ( $formats as $value ) {
			$validator = new Date( $value );
			$this->assertFalse( $validator->isValid());
		}
	}
	public function testValidFormat( ) {
		$value = '2014-01-01';
		$validator = new Date( $value );
		$this->assertTrue( $validator->isValid());
	}
} 