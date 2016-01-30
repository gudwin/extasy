<?php

namespace Extasy\tests\Schedule;

use \Extasy\Schedule\Job;
use Faid\DBSimple;

class JobTest extends BaseTest {
	/**
	 * @expectedException \LogicException
	 */
	public function testOnlyNewStatusAllowedToRun( ) {

		$job = new TestAction();
		$job->status->setValue( Job::ActiveStatus );
		$job->run();
	}
	public function testStatusChanges( ) {
		$job = new TestAction();
		$this->assertEquals( Job::NewStatus, $job->status->getValue());
		$job->run();
		$this->assertEquals( Job::FinishedStatus, $job->status->getValue());


	}
	public function testWithSameHashInsertedOnlyOnce() {
		$hash = 'some_uniq_hash';
		$job = new Job();
		$job->hash = $hash;
		$job->insert();
		$job2 = new Job();
		$job2->hash = $hash;
		$job2->insert();
		$this->assertEquals( $job->id->getValue(), $job2->id->getValue() );

	}
	public function testFactoryMethod() {
		$job = new TestAction();
		$job->insert();
		//
		$row = DBSimple::get( TestAction::TableName, array('id' => $job->id->getValue()));
		$newJob = Job::factory( $row );
		//
		$this->AssertEquals( get_class( $job ), get_class($newJob) );
	}

	/**
	 * @expectedException \NotFoundException
	 */
	public function testUnknownOnFactoryById() {
		Job::factoryById( 0 );
	}
	public function testFactoryByIdMethod() {
		$job = new TestAction();
		$job->insert();
		$newJob = Job::factoryById( $job->id->getValue());
		$this->AssertEquals( get_class( $job ), get_class($newJob) );
	}

}
 