<?php

namespace Extasy\Schedule\Api;

use Extasy\Schedule\Job;
use \Extasy\Validators\ReadableFile;
use Faid\DB;

class RestartServer extends BaseScheduleApi {
	const MethodName = 'schedule.restartServer';
	const ConfigureKey = 'Schedule.AutoloadScriptPath';
	protected function action() {
		$this->loadConfigurationFile();
	}
	protected function loadConfigurationFile() {
		try {
			$sql = sprintf('truncate %s ', Job::TableName );
			DB::post( $sql );

			$path = \Faid\Configure\Configure::read( self::ConfigureKey );
			$validator = new ReadableFile( $path );
			if ( $validator->isValid() ) {
				include $path;
			}

			\CMSLog::addMessage( __CLASS__, 'Schedule restarted');
		} catch (\Exception  $e ) {
			\CMSLog::addMessage(__CLASS__, $e );
			return ['error' => 'internal error'];
		}
		return ;
	}
} 