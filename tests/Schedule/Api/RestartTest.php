<?php

namespace Extasy\tests\Schedule\Api;

use \Extasy\tests\Schedule\TestAction;
use \Extasy\Schedule\Api\Restart;
use Faid\DBSimple;

class RestartTest extends \Extasy\tests\Schedule\BaseTest {
	/**
	 * @expectedException \NotFoundException
	 */
	public function testUnknownId() {
		$api = new Restart( [ 'id' => -1, 'actionDate' => '' ] );
		$api->exec();
	}

	public function testNewTasksNotRestarted() {
		$job = new TestAction();
		$job->insert();
		//
		$api = new Restart( [ 'id' => $job->id->getValue(),'actionDate' => '' ] );
		$api->exec();
		//
		$this->assertEquals( 1, DBSimple::getRowsCount( TestAction::TableName ) );
	}

	public function testNewTaskCreated() {
		$fixture = 'some_fixture';
		//
		$job         = new TestAction();
		$job->status = TestAction::FinishedStatus;
		$job->hash   = $fixture;
		$job->insert();
		//
		$api   = new Restart( [ 'id' => $job->id->getValue(), 'actionDate' => '' ] );
		$newId = $api->exec();
		//
		$newJob = TestAction::getById( $newId );
		$this->assertEquals( $newJob->hash->getValue(), $fixture );
		$this->assertEquals( $newJob->status->getValue(), TestAction::NewStatus );

	}
} 