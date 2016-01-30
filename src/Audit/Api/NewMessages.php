<?php

namespace Extasy\Audit\Api;

use Extasy\Audit\Record;
class  NewMessages extends ApiOperation {
	const MethodName = 'Audit.NewMessages';
	protected function action( ) {
		return Record::getNewCount();
	}
}