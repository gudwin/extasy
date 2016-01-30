<?php


namespace Extasy\tests\Validators;

use \Extasy\Validators\IsModelClassNameValidator;

class IsModelClassNameValidatorTest extends \Extasy\tests\BaseTest {
	const ClassName = '\\Extasy\\tests\\sitemap\\IsModelClassNameValidatorTest';
	public function testUnknownModel( ) {
		$validator = new IsModelClassNameValidator( ModelConfigValidatorTest::UnknownModelName);
		$this->assertFalse( $validator->isValid() );
	}
	public function testNotModelClass( ) {
		$validator = new IsModelClassNameValidator( self::ClassName  );
		$this->assertFalse( $validator->isValid() );
	}
	public function testValid( ) {
		$validator = new IsModelClassNameValidator( TestModel::ModelName );
		$this->assertTrue( $validator->isValid() );
	}
}
 