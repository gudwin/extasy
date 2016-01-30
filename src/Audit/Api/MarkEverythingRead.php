<?php

namespace Extasy\Audit\Api;

use \Faid\DBSimple;
use \Extasy\Audit\Record;

class  MarkEverythingRead extends ApiOperation {
	const MethodName = 'Audit.MarkEverythingRead';

	protected function action() {
		DBSimple::update( Record::tableName,
						  array(
							  'viewed' => 1
						  ),
						  array() );
		\CMSLog::addMessage( __CLASS__, sprintf( 'All log records set as read' ) );
	}
}