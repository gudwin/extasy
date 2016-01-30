<?php

namespace Extasy\Audit\Api;

use \Extasy\Api\ApiOperation as baseApiOperation;
use \Extasy\Api\ApiController;
class  ApiOperation extends baseApiOperation {
	const RightName = 'Auditor';
	public function __construct( $data = array( )) {
		parent::__construct( $data );
		$this->requiredACLRights = array( self::RightName );
	}
	public static function startUp() {
		$api = ApiController::getInstance();
		$api->add( new AddLogRecord());
		$api->add( new EditLog());
		$api->add( new GetSettings());
		$api->add( new SetupSettings());
		$api->add( new Logs());
		$api->add( new Records());
		$api->add( new NewMessages());
		$api->add( new SetupPriority());
		$api->add( new MarkEverythingRead());
	}
}