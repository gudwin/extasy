<?php

namespace Extasy\Audit\Api;

use \CConfig;
use \Extasy\Audit\Record;
use \Extasy\Audit\Log;

class SetupPriority extends ApiOperation {
	const MethodName = 'Audit.SetupPriority';
	const RightName = \CMSAuth::SystemAdministratorRoleName;
	protected $requiredParams = array( 'name', 'priority' );

	protected function action() {
		$log = Log::getByName( $this->getParam( 'name' ) );
		$log->critical->setValue( intval( $this->getParam( 'priority' ) ) );
		$log->update();
		\CMSLog::addMessage( __CLASS__,
							 sprintf( 'Log "%s" priority changed to - "%d"',
									  $this->getParam( 'name' ),
									  $this->getParam( 'priority' ) ) );
		return true;
	}
}