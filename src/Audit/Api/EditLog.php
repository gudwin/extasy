<?php

namespace Extasy\Audit\Api;

use \Extasy\Audit\Log;

class  EditLog extends ApiOperation {
	const RightName = \CMSAuth::SystemAdministratorRoleName;
	const MethodName = 'Audit.EditLog';
	protected $requiredParams = array( 'name', 'enable_logging' );

	protected function action() {
		$log = Log::getByName( $this->getParam( 'name' ) );
		$log->obj_enable_logging->setValue( intval( $this->getParam( 'enable_logging' ) ) );
		$log->update();
		\CMSLog::addMessage( __CLASS__,
							 sprintf( 'Log `%s` logging updated. Logging set to - "%d" ',
									  $this->getParam( 'name' ),
									  $this->getParam( 'enable_logging' )
							 ) );
		return true;
	}
}