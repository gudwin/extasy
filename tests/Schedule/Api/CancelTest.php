<?php

namespace Extasy\tests\Schedule\Api;

use \Extasy\Schedule\Api\Cancel;
use \Extasy\tests\Schedule\TestAction;
class CancelTest extends \Extasy\tests\Schedule\BaseTest{
	/**
	 * @expectedException \NotFoundException
	 */
	public function testOnUnknownTask() {
		$api = new Cancel( ['id' => 0]);
		$api->exec();
	}
	public function testOnlyNewTasksCanBeCanceled() {
		$action = new TestAction();
		$action->status = TestAction::ActiveStatus ;
		$action->insert();
		//
		$api = new Cancel( ['id' => $action->id->getValue()]);
		$api->Exec();

		$action = TestAction::getById( $action->id->getValue());
		$this->assertEquals( TestAction::ActiveStatus, $action->status->getValue());
	}
	public function testStatusSetToCanceled() {
		$fixture = 'my_uniq_fixture';
		//
		$job = new TestAction();
		$job->status = TestAction::NewStatus;
		$job->hash = $fixture;
		$job->insert();
		//
		$api = new Cancel(['id' => $job->id->getValue()]);
		$api->Exec();
		//
		$job = TestAction::getById( $job->id->getValue());
		$this->AssertEquals( TestAction::CanceledStatus, $job->status->getValue());
		$this->assertEquals( $fixture, $job->hash->getValue());
	}
} 