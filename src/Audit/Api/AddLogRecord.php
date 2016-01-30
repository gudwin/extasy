<?php

namespace Extasy\Audit\Api;

use Extasy\Audit\Record;
use Extasy\Audit\Log;
class  AddLogRecord extends ApiOperation {
	const MethodName = 'Audit.AddLogRecord';
	protected $requiredParams = array('event','short','full');

	protected function action() {
		// event should exists
		Log::getByName( $this->GetParam( 'event' ));

		Record::add( $this->GetParam( 'event' ),$this->GetParam( 'short' ),$this->GetParam( 'full' ));
		return true;
	}
}