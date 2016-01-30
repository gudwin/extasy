<?php
namespace Extasy\Audit;

use \Extasy\Schedule\Job;
use \Faid\DBSimple;

class Cleaner {
	const ClassName = '\\Extasy\\Audit\\Cleaner';
	const Limit     = 5000;
	const OutputDir = 'Environment/logs/audit/';
	protected static $maximumLogLength = 0;


	public static function pack() {
		$count = DBSimple::getRowsCount( Record::tableName );
		if ( self::IsLogTooBig( $count ) ) {
			self::storeExtraRecords( );
		}
	}

	protected static function isLogTooBig( $count ) {
		$register               = new \SystemRegister( \Extasy\Audit\Api\SetupSettings::RegisterPath );
		self::$maximumLogLength = intval( $register->maximumLogLength->value );
		return $count > self::$maximumLogLength;
	}

	protected static function storeExtraRecords(  ) {
		\CMSLog::addMessage(__CLASS__ , 'Cleaning log table');
		$found = DBSimple::select( Record::tableName, array(), sprintf( 'id desc limit %d,1', self::$maximumLogLength ));
		if ( empty( $found ) ) {
			return;
		}

		$data   = DBSimple::select( Record::tableName,
									'1',
									sprintf( 'id asc limit 0,%d', self::$maximumLogLength ) );

		if ( !empty( $data ) ) {
			$lastId = $data[sizeof( $data) - 1 ]['id'];
			$fileName = sprintf( '%s%s%s.log', SYS_ROOT, self::OutputDir, date( 'Y-m-d H:i:s' ) );
			file_put_contents( $fileName, json_encode( $data ) );
			DBSimple::delete( Record::tableName,
							  array(
								  sprintf( 'id <= %d', $lastId )
							  ) );
            \CMSLog::addMessage(__CLASS__, 'Extra messages exported in local folder: '. $fileName);
		}


	}
}