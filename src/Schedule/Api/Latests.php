<?php

namespace Extasy\Schedule\Api;


use Extasy\Schedule\Job;
use Faid\Configure\Configure;
use Faid\DBSimple;

class Latests extends BaseScheduleApi {
	const MethodName = 'schedule.latests';
	const ConfigureKey = 'Schedule.latestCount';
	const DefaultLimit = 100;
	protected $limit = 0;
	public function __construct( $data = [] ) {
		parent::__construct( $data );
		try {
			$this->limit = Configure::read( self::ConfigureKey );
		} catch (\Exception $e ) {
			$this->limit = self::DefaultLimit;
		}
	}
	protected function action() {
		$orderCond = sprintf('id desc limit 0,%d', $this->limit );
		$data = DBSimple::select( Job::TableName, [], $orderCond );

		foreach ( $data as $key => $row ) {
			$job = Job::factory( $row );
			$data[ $key ] = $job->getPreviewParseData();
		}
		return $data;
	}
} 