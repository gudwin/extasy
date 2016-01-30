<?php
abstract class baseSystemRegisterTest extends PHPUnit_Framework_TestCase {
	public function setUp() {
		$dumper = new CDumper();
		$content = file_get_contents(dirname(__FILE__).'/import.sql');
		$dumper->import($content);
		$this->clearSystemRegister();
	}
	protected function clearSystemRegister( ) {
		\Extasy\tests\system_register\Restorator::restore();
	}
}