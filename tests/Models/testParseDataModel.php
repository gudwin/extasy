<?php
namespace Extasy\tests\Models {
	class testParseDataModel extends \Extasy\Model\Model {
		const TableName = '';
		const ModelName = '\\Extasy\\tests\\Models\\testParseDataModel';

		const StringMethodName = 'string_method_name_example';
		const FunctionMethodName = 'function_method_name';
		const InvalidMethodName = 'invalid_method';

		public function getViewValueByKey( $key ) {
			return $this->getParseDataByKey( $key );
		}
		public static function getFieldsInfo() {
			return array(
				'table'  => self::TableName,
				'fields' => array(
					'id'      => '\\Extasy\\Columns\\Index',
					'name'    => array(
						'class'        => '\\Extasy\\Columns\\Input',
						'preview_field' => array(
							'value' => 'getValue',
							'view'  => 'getViewValue',
						),
						'parse_field'    => true,
						self::StringMethodName => 'getValue',
					),
					'content' => array(
						'class'     => '\\Extasy\\Columns\\Input',
						'parse_field' => true,
						self::StringMethodName => true,
						self::FunctionMethodName => function ($column ) {
							return !empty( $column->getValue() );
						},
						self::InvalidMethodName => 'unknown_method'
					),
				)
			);
		}
	}
}