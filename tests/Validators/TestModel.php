<?php
namespace Extasy\tests\Validators;
class TestModel extends \RegisteredDocument {
	const ModelName            = '\\Extasy\\tests\\Validators\\TestModel';
	const ExistingPropertyName = 'existing_property';
	const UnknownPropertyName  = 'unknown_property';
	const PathPropertyName     = 'path';
	const SubPathPropertyName  = 'subpath';
	const Fixture              = 'Hello world!';

	public static function getFieldsInfo() {
		return array(

			self::ExistingPropertyName => self::Fixture,
			self::PathPropertyName     => array(
				self::SubPathPropertyName => self::Fixture,
			)

		);
	}
} 