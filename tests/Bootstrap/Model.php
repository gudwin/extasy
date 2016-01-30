<?php
namespace Extasy\tests\Bootstrap {
	class Model extends \Extasy\Model\Model {
		const TableName = 'test_document';
		const ModelName = '\\Extasy\\tests\\Bootstrap\\Model';
		public static function getFieldsInfo() {
			return array(
				'table' => self::TableName,
				'fields' => array(
					'id' => '\\Extasy\\Columns\\Index',
				)
			);
		}
	}
}