<?php
namespace Extasy\Schedule\Api;


class StopServer extends BaseScheduleApi {
	const MethodName = 'schedule.stopServer';
	protected $requiredParams = [ 'runningFlag' ];

	protected function action() {
		$register = new \SystemRegister('System/Schedule');
		$register->runningFlag->value = intval( $this->getParam('runningFlag'));
		\SystemRegisterSample::createCache();
	}
} 