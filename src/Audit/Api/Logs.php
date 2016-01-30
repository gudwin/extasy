<?php

namespace Extasy\Audit\Api;

use Extasy\Audit\Log;
class  Logs extends ApiOperation {
	const MethodName = 'Audit.Logs';
	protected function action( ) {
		$data = Log::selectAll();

		$result = array();
		foreach ( $data as $row ) {
			$result[] = array(
				'name' => $row->name->getViewValue(),
				'description' => $row->description->getViewValue(),
				'critical' => $row->critical->getValue(),
				'enable_logging' => $row->enable_logging->getValue(),
			);
		}
		return $result;
	}
}