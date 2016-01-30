<?php

namespace Extasy\tests\Schedule\Api;

use \Extasy\Schedule\Api\ServerStatus;


class ServerStatusTest extends  \Extasy\tests\Schedule\BaseTest {
	public function testStatuses() {
		$register = new \SystemRegister('System/Schedule');
		$register->runningFlag->value = 1;
		\SystemRegisterSample::createCache();
		//
		$api = new ServerStatus();
		$this->assertEquals( 1, $api->exec());
		//
		$register = new \SystemRegister('System/Schedule');
		$register->runningFlag->value = 0;
		\SystemRegisterSample::createCache();

		$api = new ServerStatus();
		$this->assertEquals( 0, $api->exec());
	}
} 