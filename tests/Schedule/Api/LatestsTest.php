<?php

namespace Extasy\tests\Schedule\Api;


use Extasy\tests\Schedule\TestAction;
use Faid\Configure\Configure;
use Extasy\Schedule\Api\Latests;

class LatestsTest extends \Extasy\tests\Schedule\BaseTest{
	public function testLastRecordsReturned() {
		$count = 2;

		$data = [
			'first',
			'second',
			'third'
		];
		foreach ( $data as $row ) {
			$job = new TestAction();
			$job->hash = $row;
			$job->insert();
		}

		Configure::write( Latests::ConfigureKey, $count );

		$api = new Latests();
		$result = $api->exec();
		//
		$this->assertTrue( is_array( $result ));
		$this->assertEquals( 2, sizeof( $result ));
		// check order
		$this->assertEquals( 3, $result[0]['id']);
		$this->assertEquals( 'third', $result[0]['hash']);
		$this->assertEquals( 2, $result[1]['id']);
		$this->assertEquals( 'second', $result[1]['hash']);

	}
} 