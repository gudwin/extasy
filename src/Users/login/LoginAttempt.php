<?php

namespace Extasy\Users\login;

use \Faid\DB;

class LoginAttempt extends \Extasy\Model\Model {
	const SuccessStatus = 1;
	const FailStatus    = 0;
	const TableName     = 'login_attempts';
	const ModelName     = __CLASS__;

	public static function quickRegister( $user, $authPassed, $method = 'default' ) {
		if ( !empty( $_SERVER[ 'REMOTE_ADDR' ] ) ) {
			$ip = $_SERVER[ 'REMOTE_ADDR' ];
			if ( '::1' == $ip ) {
				$ip = '127.0.0.1';
			}
		} else {
			$ip = '127.0.0.1';
		}

		$userId = !empty( $user ) ? $user->id->getValue() : 0;
		\Faid\DBSimple::insert( self::TableName,
								array(
									'host'    => ip2long( $ip ),
									'date'    => date( 'Y-m-d H:i:s' ),
									'user_id' => $userId,
									'status'  => !empty( $authPassed ) ? self::SuccessStatus : self::FailStatus,
									'method'  => $method
								) );
		\CMSLog::addMessage( __CLASS__,
							 sprintf( 'Login attemp into user with id="%d", operation status - %s',
									  $userId,
									  !empty( $authPassed ) ? 'success' : 'fail'
							 ) );


	}

	public function createDatabaseTable( $dropTable = false ) {
		parent::createDatabaseTable( $dropTable );

		$sql = sprintf( 'alter table %s add index `login_status` (`user_id`, `status`)', self::TableName );
		DB::post( $sql );
	}

	public static function getFieldsInfo() {
		return array(
			'table'  => self::TableName,
			'fields' => array(
				'id'      => '\\Extasy\\Columns\\Index',
				'host'    => '\\Extasy\\Columns\\IP',
				'date'    => '\\Extasy\\Columns\\Datetime',
				'user_id' => '\\Extasy\\Users\\Columns\\Username',
				'status'  => '\\Extasy\\Columns\\Number',
				'method'  => '\\Extasy\\Columns\\Input'
			)
		);
	}
} 