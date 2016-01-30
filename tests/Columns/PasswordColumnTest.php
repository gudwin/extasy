<?php

namespace Extasy\tests\Columns;

use \Extasy\Columns\Password;

class PasswordColumnTest extends \Extasy\tests\BaseTest {
	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testItemsCount() {
		Password::validatePassword( '1234567');
	}
	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testAllSymbolsPresent() {
		Password::validatePassword( '1234567890#');
	}
	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testNoSymbolDuplicates() {
		Password::validatePassword( '1234aaa#');
	}
	public function testNormalValue() {
		Password::validatePassword( 'abc123\\#');
	}

} 