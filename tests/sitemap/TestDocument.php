<?php
namespace Extasy\tests\sitemap;

class TestDocument extends \Extasy\Model\Model {
    protected $withSitemap = true;
	const ModelName = '\\Extasy\\tests\\sitemap\TestDocument';
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