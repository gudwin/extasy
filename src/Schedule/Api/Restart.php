<?php

namespace Extasy\Schedule\Api;


use Extasy\tests\Schedule\TestAction;
use \Extasy\Schedule\Job;
use Faid\DBSimple;

class Restart extends BaseScheduleApi {
	const MethodName = 'schedule.restart';
	protected $requiredParams = [ 'id', 'actionDate' ];

	protected function action() {
		$originalJob = \Extasy\Schedule\Job::factoryById( $this->getParam('id'));
		if ( Job::NewStatus == $originalJob->status->getValue() ) {
			return $originalJob->id->getValue();
		}

		$className = get_class($originalJob);
		$job = new $className();
		$job->hash = $originalJob->hash->getValue();
		$job->actionDate->setTime( $this->getParam('actionDate'));
		$job->data = $originalJob->data->getValue();
		$job->insert();
		return $job->id->getValue();
	}
} 