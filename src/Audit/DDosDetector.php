<?php


namespace Extasy\Audit;

use \Faid\DB;

class DDosDetector {
	const TableName = 'ddos_detector';

	const SystemRegisterPath = '/System/Security/DDosDetector';
	protected static $ip = '';
	protected static $config = array( 'MaxConnections' => 0,
									  'Period'         => '1 MINUTE',
									  'Message'        => 'Too many connections from one IP'
	);

	public static function detect() {
		self::loadConfig();
		if ( !empty( $_SERVER[ 'REMOTE_ADDR' ] ) ) {
			self::$ip = $_SERVER[ 'REMOTE_ADDR' ];
			self::storeConnection();

			if ( self::isLimitExceeded() ) {
				\CMSLog::addMessage(__CLASS__, sprintf( 'DDOS attempt detected. Ip - "%s" ', self::$ip));
				die( self::$config[ 'Message' ] );
			}
		}
	}

	protected static function loadConfig() {
		$register     = new \SystemRegister( self::SystemRegisterPath );
		self::$config = \SystemRegisterHelper::exportData( $register->getId() );
	}

	protected static function storeConnection() {
		$sql = sprintf( 'insert into %s set `ip` = "%d", `date` = "%s"',
						self::TableName,
						DB::escape( ip2long(self::$ip )),
						DB::escape( date( 'Y-m-d H:i:s' ) )

		);
		DB::post( $sql );
	}

	protected static function isLimitExceeded() {
		$sql  = sprintf( ' select count(*) as `count` from %s where `ip`="%d" ',
						 self::TableName,
						 DB::escape( ip2long(self::$ip ))
		);
		$data = DB::get( $sql );
		if ( !empty( $data ) ) {
			$result = $data[ 'count' ];
		} else {
			$result = 0;
		}
		self::cleanDB();

		return $result >= self::$config['MaxConnections'];
	}

	protected static function cleanDB() {
		$sql = sprintf( ' delete from %s where `date` < "%s"',
						self::TableName,
						date('Y-m-d H:i:s',strtotime( '-' . self::$config[ 'Period' ] ))
		);
		DB::post( $sql );
	}
} 