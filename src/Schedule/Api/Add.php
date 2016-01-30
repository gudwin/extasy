<?php

namespace Extasy\Schedule\Api;


class Add extends BaseScheduleApi {
	const MethodName = 'schedule.add';
	protected $className = '';
	protected $actionDate = '';
	protected $requiredParams = ['class','hash','actionDate'];
	protected function action() {
		$this->className = $this->getParam( 'class' );
		$this->hash = $this->getParam('hash');
		$this->actionDate = $this->getParam('actionDate');

		\Extasy\Schedule\Columns\ClassName::validateClassName( $this->className );

		$job = new $this->className();
		$job->actionDate->setTime( $this->actionDate );
		$job->hash = $this->hash;
		$job->insert( );
		return $job->id->getValue();
	}
} 