<?php
/**
 * User: gisma
 * Date: 25.11.14 / Time: 3:54
 */

namespace Extasy\tests\Schedule\Columns;

use \Extasy\Schedule\Columns\ActionDate;

class ActionDateTest extends \Extasy\tests\Schedule\BaseTest {
	public function testDateIncorrect() {
		$baseTime = time();
		$column = new ActionDate('test', [], '');
		$column->setTime('123');
		$time = strtotime( $column->getValue() ) ;

		$this->assertTrue( $time - $baseTime >= 0 );
	}
	public function testSetTime() {
		$baseTime = time();
		$column = new ActionDate('test', [], '');
		$column->setTime('1 minute');
		$time = strtotime( $column->getValue() );
		$this->assertTrue( $time - $baseTime >= 60 );
	}
} 