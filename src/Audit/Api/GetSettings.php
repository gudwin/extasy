<?php

namespace Extasy\Audit\Api;

use \Extasy\Audit\Record;
use \CConfig;
class  GetSettings extends ApiOperation {
	const MethodName = 'Audit.GetSettings';
	protected function action( ) {
		$schema = CConfig::getSchema( Record::CriticalEmailSchemaName );
		$notificationEmails = $schema->getControlByName('to');
		$register = new \SystemRegister( SetupSettings::RegisterPath );
		return array(
			'notification_emails' => $notificationEmails->getValue(),
			'maximumLogLength' => $register->maximumLogLength->value
		);
	}
}