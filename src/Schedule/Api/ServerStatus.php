<?php

namespace Extasy\Schedule\Api;


class ServerStatus extends BaseScheduleApi {
	const MethodName = 'schedule.serverStatus';
	protected function action() {
		$register = new \SystemRegister('System/Schedule');
		return $register->runningFlag->value;
	}
} 