<?php
namespace Extasy\tests\Models {
	class testModel extends \Extasy\Model\Model {
		const TableName = 'test_model';
		const ModelName = '\\Extasy\\tests\\Models\\testModel';

		public static function getFieldsInfo() {
			return array(
				'table'  => self::TableName,
				'fields' => array(
					'id'   => '\\Extasy\\Columns\\Index',
					'name' => '\\Extasy\\Columns\\Input'
				),
			);
		}
	}
}