<?php

namespace Extasy\tests\Audit;

use \Faid\DBSimple;
use \Extasy\Audit\Record;
use \Extasy\Audit\Api\MarkEverythingRead;

class MarkEverythingApiRead extends base {
	public function testUpdateAll( ) {
		$this->assertEquals( 0, $this->getViewedCount() );
		//
		$api = new MarkEverythingRead();
		$api->exec();
		//
		$this->assertEquals( 3, $this->getViewedCount() );
	}
	protected function getViewedCount( ) {
		return DBSimple::getRowsCount( Record::tableName, array('viewed' => '1') );
	}
} 