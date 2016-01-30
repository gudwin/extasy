<?php

namespace Extasy\tests\Schedule\Api;

use \Extasy\Schedule\Api\Add;
use Extasy\Schedule\Job;
use \Extasy\tests\Schedule\TestAction;
class AddTest extends \Extasy\tests\Schedule\BaseTest {
	/**
	 * @expectedException \NotFoundException
	 */
	public function testWithUnknownClass() {
		$fixture = '\\Some\\Unknown\\Class';
		$api = new Add(['class' => $fixture ,'actionDate' => '','hash' => '' ]);
		$api->exec();
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testWithNotAJobClass() {
		$api = new Add(['class' => __CLASS__ ,'actionDate' => '','hash' => '' ]);
		$api->exec();
	}
	public function testAdd() {
		$fixture = 'hello world!';
		$api = new Add( ['class' => TestAction::ModelName, 'actionDate' => '','hash' => $fixture ]);
		$id = $api->exec( );

		$new = Job::factoryById( $id );
		$this->assertEquals( $new->status->getValue(), TestAction::NewStatus );
		$this->assertEquals( $new->class->getValue(), TestAction::ModelName );
		$this->assertEquals( $new->hash->getValue(), $fixture);
	}
}