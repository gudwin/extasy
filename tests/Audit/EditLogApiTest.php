<?php

namespace Extasy\tests\Audit;
use Extasy\Audit\Log;
use Extasy\Audit\Api\EditLog;
class EditLogApiTest extends base {
	const EmailFixture = 'test@test.com,dmitry@dd-team.org';
	public function testRequiredFields( ) {
		$fields = array('name','enable_logging');
		foreach ( $fields as $key=>$row ) {
			$tmp = $fields;
			unset( $tmp[ $key ]);
			//
			$api  = new EditLog( );
			$api->setParamsData( $tmp );
			try {
				$api->exec();
				$this->fail();
			} catch (\Extasy\Api\Exception $e ) {
			}
		}

	}
	public function testSetupLogging( ) {
		$api  = new EditLog( array('name' => 'Log1','enable_logging' => 0));
		$api->exec();

		$log = new Log();
		$log->get( 1 );
		$this->assertEquals( 0, $log->enable_logging->getValue());

		$api  = new EditLog( array('name' => 'Log1','enable_logging' => 1));
		$api->exec();
		//
		$log = new Log();
		$log->get( 1 );
		$this->assertEquals( 1, $log->enable_logging->getValue());
	}
}