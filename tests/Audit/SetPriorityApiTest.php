<?php

namespace Extasy\tests\Audit;
use Extasy\Audit\Log;
use Extasy\Audit\Api\SetupPriority;
use Extasy\Audit\Api\Logs;
class SetPriorityApiTest extends base {
	public function testRequiredParams( ) {
		$fields = array('name','priority');
		foreach ( $fields as $key=>$row ) {
			$tmp = $fields;
			unset( $tmp[ $key ]);
			//
			$api  = new SetupPriority( );
			$api->setParamsData( $tmp );
			try {
				$api->exec();
				$this->fail();
			} catch (\Extasy\Api\Exception $e ) {
			}
		}
	}
	public function testSetValue( ) {
		$api  = new SetupPriority( );
		$api->setParamsData( array('name' => 'Log1', 'priority' => 1) );
		//
		$api->exec();
		//
		$log = Log::getByName( 'Log1' );
		$this->assertEquals( true, $log->critical->getValue());
	}
}