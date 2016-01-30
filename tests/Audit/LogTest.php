<?php

namespace Extasy\tests\Audit;
use \Extasy\Audit\Log;
class LogTest extends base {
	public function testSelectAll( ) {
		$result = Log::selectAll();
		$this->assertEquals( 2, sizeof( $result )) ;
		foreach  ( $result as $row ) {
			$this->assertTrue( $row instanceof \Extasy\Audit\Log );
		}
	}

	/**
	 * @expectedException \NotFoundException
	 */
	public function testGetByUnknownId( ) {
		Log::getById(-1);
	}
	public function testGetById( ) {
		$log = Log::getById( 1 );
		$this->assertTrue( $log instanceof Log );
		$this->assertEquals( 1 , $log->id->getValue() );
	}
	public function testSetupLogging( ) {
		$log = Log::getById( 1 );
		$log->setupLogging( 1 );
		$this->assertEquals( true, $log->enable_logging->getValue() );
	}
}