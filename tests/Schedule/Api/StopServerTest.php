<?php


namespace Extasy\tests\Schedule\Api;

use \Extasy\Schedule\Api\StopServer;
class StopServerTest extends \Extasy\tests\Schedule\BaseTest {
	public function testSystemRegisterValueChanged() {
		$api = new StopServer(array('runningFlag' => 1));
		$api->exec();
		$register = new \SystemRegister('System/Schedule');
		$this->assertEquals( $register->runningFlag->value, 1 );

		$api = new StopServer(array('runningFlag' => 0));
		$api->exec();
		$register = new \SystemRegister('System/Schedule');
		$this->assertEquals( $register->runningFlag->value, 0 );
	}
} 