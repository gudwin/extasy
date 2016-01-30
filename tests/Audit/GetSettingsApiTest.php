<?php

namespace Extasy\tests\Audit;

use Extasy\Audit\Api\GetSettings;
class GetSettingsApiTest extends base {
	public function testGetInformation( ) {
		$api = new GetSettings();
		$result = $api->exec();
		$this->assertEquals( 'dmitry@dd-team.org', $result['notification_emails'] );
	}
}