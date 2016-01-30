<?php

namespace Extasy\Schedule\Api;


use Extasy\Schedule\Job;

class Cancel extends BaseScheduleApi{
	const MethodName = 'schedule.cancel';
	protected $requiredParams = ['id'];
	protected function action( ) {
		$job = Job::factoryById( $this->getParam('id'));
		if ( $job->status->getValue() != Job::NewStatus ) {
			return ;
		}
		$job->status = Job::CanceledStatus;
		$job->update();
	}
} 