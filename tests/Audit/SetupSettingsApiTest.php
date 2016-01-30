<?php

namespace Extasy\tests\Audit;

use Extasy\Audit\Api\GetSettings;
use Extasy\Audit\Api\SetupSettings;

class SetupSettingsApiTest extends base {
	const EmailFixture = 'test@test.com,dmitry@dd-team.org';
	public function testSetupEmails() {
		//
		$api = new GetSettings( );
		$result = $api->exec();
		$this->assertEquals('dmitry@dd-team.org', $result['notification_emails'] );

		$api = new SetupSettings();
		$api->setParamsData(
			array(
				 'notification_emails' => self::EmailFixture,
				 'maximumLogLength' => '',
			)
		);
		$api->exec();
		//
		$api = new GetSettings( );
		$result = $api->exec();
		$this->assertEquals(self::EmailFixture, $result['notification_emails'] );
	}
}