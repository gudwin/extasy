<?php
namespace Extasy\tests\Schedule {
	class TestAction extends \Extasy\Schedule\Job {
		const ModelName = __CLASS__;
		protected static $isCalled = false;
		protected function action() {
			self::$isCalled = true;
		}
		public static function setUp( ) {
			self::$isCalled = false;
		}
		public static function isCalled( ) {
			return self::$isCalled;
		}
	}

}