<?php

namespace Extasy\Schedule;

use \Faid\Configure\Configure;
use \Extasy\Audit\Record;
use Faid\Configure\ConfigureException;
use Faid\DBSimple;


class Runner extends \Faid\Controller\Controller {
	const TimeoutConfigureKey = 'Schedule.timeout';
	const LogName = 'Schedule.runtimeTime';
	const DeletePeriod = '1 day';

	public function resolveJobs( ) {
		if ( $this->noNeedToRun() ) {
			return ;
		}
		$timeLimit = $this->getTimeLimit();
		$startTime = time();
		do {
			try {
				$job = $this->getLastJob();
				if ( is_object( $job )) {
					$job->run();
				}
			} catch (\Exception $e ) {
				Record::add( self::LogName, $e );
				if ( !empty( $job )) {
					$job->status = Job::ErrorStatus;
					$job->update();
				}


			}

			$timeToStop = !empty( $timeLimit ) ? ( time() >= $startTime + $timeLimit) : true;
			if ( !$timeToStop ) {
				sleep( 1 );
			}
		} while ( !$timeToStop );
		$this->deleteOldRows( );
	}
	public function noNeedToRun() {
		$register = new \SystemRegister('/System/Schedule');
		return $register->runningFlag->value == 0;
	}
	public function getLastJob() {
		$jobData = DBSimple::get( Job::TableName, array(
			'status' => Job::NewStatus,
			 'actionDate <= NOW() '
		),'order by `id` asc');
		if ( !empty( $jobData )) {
			return Job::factory( $jobData );
		}
	}
	protected function getTimeLimit( ) {
		try {
			$result = Configure::read( self::TimeoutConfigureKey );
		} catch ( ConfigureException $e ) {
			$result = 1;
		}
		return $result;
	}
	protected function deleteOldRows( ) {
		DBSimple::delete( Job::TableName, array(
			sprintf( '`actionDate` <= NOW() - INTERVAL %s', static::DeletePeriod)
		));
	}

} 