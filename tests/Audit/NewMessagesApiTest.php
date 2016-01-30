<?php

namespace Extasy\tests\Audit;

use Extasy\Audit\Api\NewMessages;
use Extasy\Audit\Api\Records;

class NewMessagesApiTest extends base {
	const DefaultNewCount = 3;
	public function testGetDefaultCount() {
		$api = new NewMessages();
		$this->assertEquals( self::DefaultNewCount, $api->exec());
	}
	public function testValueReflectsViews( ) {
		$api = new NewMessages();
		$this->assertEquals( self::DefaultNewCount, $api->exec());
		//
		$recordsApi = new Records();
		$recordsApi->setParamsData(array('limit' => 1));
		$recordsApi->exec();
		//
		$api = new NewMessages();
		$this->assertEquals( self::DefaultNewCount - 1, $api->exec());
	}
}