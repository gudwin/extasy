<?php

namespace Extasy\Audit\Api;

use \CConfig;
use \Extasy\Audit\Record;

class SetupSettings extends ApiOperation {
	const RightName = \CMSAuth::SystemAdministratorRoleName;
	const MethodName   = 'Audit.SetupSettings';
	const RegisterPath = '/System/Audit/';
	const LogName      = 'Audit.SetupSettings';
	protected $requiredParams = array( 'notification_emails', 'maximumLogLength' );

	protected function action() {
		$schema = CConfig::getSchema( Record::CriticalEmailSchemaName );
		$result = $schema->getControlByName( 'to' );
		$result->setValue( $this->getParam( 'notification_emails' ) );
		$register                   = new \SystemRegister( self::RegisterPath );
		$register->maximumLogLength = $this->getParam( 'maximumLogLength' );
		\SystemRegisterSample::createCache();
		\CMSLog::addMessage( self::LogName,
							 sprintf( 'Audit settings updated. New log length - "%s", Notification emails account - "%s"',
									  $this->getParam( 'notification_emails' ),
									  $this->getParam( 'maximumLogLength' )
							 ) );
		return true;
	}
}