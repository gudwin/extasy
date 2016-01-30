<?php
abstract class SystemRegisterTestCase  extends PHPUnit_Framework_TestCase {
	public function setUp() {
		CDumper::importFile(dirname(__FILE__) . '/import.sql');
		SystemRegisterSample::createCache();
	}

	public function tearDown() {
		SystemRegisterSample::clearCache();
        \Extasy\tests\system_register\Restorator::restore();
	}
}