<?php
namespace Extasy\tests\acl;

class TestModel extends \RegisteredDocument {
	const ModelName = '\\Extasy\\tests\\acl\\TestModel';
	const TableName = 'test_document';
	const PermissionName = 'Permission1';
	public static function getFieldsInfo( ) {
		return array(
			'table' => self::TableName,
			'fields' => array(
				'id' => '\\Extasy\\Columns\\Index',
				'grants' => '\\GrantColumn'
			)
		);
	}
}