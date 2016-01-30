<?php

namespace Extasy\tests\Schedule;

use \Extasy\Schedule\Runner;
use \Extasy\Schedule\Job;
use Faid\DBSimple;

class RunnerTest extends BaseTest {
	public function setUp() {
		parent::setUp();
		$register = new \SystemRegister('System/Schedule');
		$register->runningFlag->value = 1;
		\SystemRegisterSample::createCache();

	}
	/**
	 * @medium
	 */
	public function testTimeout() {
		$fixture = 3;
		$this->setRunnerTimeout( $fixture );

		$startMicro = microtime( true );
		//
		$runner = new Runner();
		$runner->resolveJobs();
		//
		$this->assertEquals( $fixture, floor( microtime( true ) - $startMicro ) );

	}

	public function testActionCalled() {
		$job = new TestAction();
		$job->insert();
		//
		$this->assertFalse( TestAction::isCalled() );
		//
		$runner = new Runner();
		$runner->resolveJobs();
        //
		$this->assertTrue( TestAction::isCalled() );
	}

	public function testActionCalledInTime() {

		$job = new TestAction();
		$job->actionDate->setTime( '+1 year' );
		$job->insert();
		//
		$runner = new Runner();
		$runner->resolveJobs();

		$this->assertFalse( TestAction::isCalled() );

		$job = new TestAction();
		$job->hash = 'another'; // hash must be different or insert will have no effect
		$job->insert();

		$runner = new Runner();
		$runner->resolveJobs();

		//
		$this->assertTrue( TestAction::isCalled() );

	}

	public function testProcessedOnlyNewTasks() {
		$job = new TestAction();
		$job->status = Job::ActiveStatus;
		$job->insert();

		$runner = new Runner();
		$runner->resolveJobs();
		//
		$this->assertFalse( TestAction::isCalled() );
		//

	}

	public function testGarbageCollection() {
		$job             = new TestAction();
		$job->status     = Job::FinishedStatus;
		$job->actionDate = date( 'Y-m-d H:i:s', strtotime( '-1 day' ) );
		$job->insert();
		$this->assertEquals( 1, DBSimple::getRowsCount( Job::TableName ) );
		$runner = new Runner();
		$runner->resolveJobs();
		$this->assertEquals( 0, DBSimple::getRowsCount( Job::TableName ) );
	}

	public function testRunnerNotWorksOnSystemRegisterFlag() {
		$register = new \SystemRegister('System/Schedule');
		$register->runningFlag->value = 0;
		\SystemRegisterSample::createCache();
		//
		$job = new TestAction();
		$job->insert();
		//
		$runner = new Runner();
		$runner->resolveJobs();
		//
		$this->assertFalse( TestAction::isCalled() );

		$job = TestAction::getById( $job->id->getValue());
		$this->assertEquals( TestAction::NewStatus, $job->status->getValue());
	}



}
 