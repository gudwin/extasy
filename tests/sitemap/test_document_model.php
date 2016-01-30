<?php
use \Extasy\tests\Helper;
use \Extasy\tests\system_register\Restorator;

Restorator::restorePath('/System/', array(
	'Sitemap' => array(
		'visible' => 0,
		'sitemap.xml.disable' => 1,
		'sitemap.xml' => 1,
	),
));
SystemRegisterSample::createCache();


class Test_Document extends \Extasy\Model\Model {
	const ModelName = 'Test_Document';
	const TableName = 'sitemap_test_document';
	public static function getFieldsInfo() {
		return array(
			'table' => self::TableName,
			'fields' => array(
				'id' 	=> '\\Extasy\\Columns\\Index',
				'name'  => '\\Extasy\\Columns\\Input',
			)
		);
	}
}