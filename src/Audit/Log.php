<?php
namespace Extasy\Audit;

use \Extasy\Model\Model as extasyDocument;
use \Faid\DBSimple;

class Log extends extasyDocument {
	const ModelName = '\\Extasy\\Audit\\Log';

	const tableName = 'audit_logs';

	public function setupLogging( $enable ) {
		$this->obj_enable_logging->setValue( $enable );
		$this->update();
	}

	public static function selectAll() {
		$data   = DBSimple::select( self::tableName, null, 'name asc' );
		$result = array();
		foreach ( $data as $row ) {
			$result[ ] = new Log( $row );
		}

		return $result;
	}

	/**
	 * @param $id
	 *
	 * @return Log
	 */
	public static function getById( $id ) {
		$result = new Log();
		$found  = $result->get( $id );
		if ( empty( $found ) ) {
			throw new \NotFoundException( 'Log record not found' );
		}

		return $result;
	}

	public static function getByName( $name ) {
		$found = DBSimple::get(
						 Log::tableName,
						 array(
							 'name' => $name
						 )
		);
		if ( empty( $found ) ) {
			throw new \NotFoundException( 'Log record not found' );
		}
		$result = new Log( $found );
		return $result;
	}

	public static function createIfNotExists( $name ) {
		try {
			$result = self::getByName( $name );
		}
		catch ( \NotFoundException $e ) {
			$result = new Log();
			$result->name->setValue( $name );
			$result->insert();
			$result->setupLogging( true );
		}
		return $result;
	}

	public function getParseData() {
		$result = array(
			'id'   => $this->id->getValue(),
			'name' => $this->name->getViewValue(),
			'description' => $this->description->getViewValue()
		);
		return $result;
	}

	public static function getFieldsInfo() {
		return array(
			'table'  => self::tableName,
			'fields' => array(
				'id'             => '\\Extasy\\Columns\\Index',
				'name'           => '\\Extasy\\Columns\\Input',
				'description'    => '\\Extasy\\Columns\\Text',
				'critical'       => '\\Extasy\\Columns\\Boolean',
				'enable_logging' => '\\Extasy\\Columns\\Boolean'
			)
		);
	}
}