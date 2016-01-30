<?php

namespace Extasy\tests\Audit;

use Extasy\Audit\Api\Logs;

class LogsApiTest extends base {
	public function testDataReturned() {
		$fields = array(
			'name', 'description', 'critical', 'enable_logging'
		);
		$api    = new Logs();
		$result = $api->exec();
		$this->assertEquals(2, sizeof($result));

		foreach ($fields as $row) {
			$this->assertArrayHasKey($row, $result[ 0 ]);
		}
	}
}