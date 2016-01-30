<?php

namespace Extasy\Users\Social;

use \Faid\DBSimple;
class Network extends \Extasy\Model\Model {
	const ModelName = '\\Extasy\\Users\\Social\\Network';
	const TableName = 'social_networks';
	public static function getByName( $name ) {
		$found = DBSimple::get( self::TableName, array(
			'name' => $name
		));
		if ( empty( $found )) {
			throw new \NotFoundException('Unknown social network name - '. $name);
		}
		$result = new Network( $found );
		return $result;
	}
	public static function getFieldsInfo() {
		return array(
			'table' => self::TableName,
			'fields' => array(
				'id' => '\\Extasy\\Columns\\Index',
				'name' => '\\Extasy\\Columns\\Input',
				'title'=> '\\Extasy\\Columns\\Input',
			)
		);
	}
} 