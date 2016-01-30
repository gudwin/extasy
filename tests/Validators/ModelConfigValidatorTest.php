<?php
namespace Extasy\tests\Validators;

use \Extasy\Validators\ModelConfigValidator;

class ModelConfigValidatorTest extends \Extasy\tests\BaseTest {
	const UnknownModelName = '\\Extasy\\tests\\sitemap\\UnknownModel';
 	public function testUnknownModel( ) {
		$validator = new ModelConfigValidator( self::UnknownModelName, TestModel::ExistingPropertyName );
		$this->assertFalse( $validator->isValid());
	}
	public function testKeyNotPresent( ) {
		$validator = new ModelConfigValidator( TestModel::ModelName, TestModel::UnknownPropertyName);
		$this->assertFalse( $validator->isValid());
	}
	public function testKeyPresent( ) {
		$validator = new ModelConfigValidator( TestModel::ModelName, TestModel::ExistingPropertyName );
		$this->assertTrue( $validator->isValid());
		$this->assertEquals( TestModel::Fixture, $validator->getData() );
	}
	public function testConfigPathSupported( ) {
		$validator = new ModelConfigValidator( TestModel::ModelName, array(TestModel::PathPropertyName, TestModel::SubPathPropertyName) );
		$this->assertTrue( $validator->isValid());
		$this->assertEquals( TestModel::Fixture, $validator->getData() );
	}
} 