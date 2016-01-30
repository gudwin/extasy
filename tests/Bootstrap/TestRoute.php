<?php
/**
 * Created by PhpStorm.
 * User: gisma
 * Date: 04.04.14
 * Time: 11:10
 */

namespace Extasy\tests\Bootstrap;


class TestRoute extends \Faid\Dispatcher\HttpRoute {
	protected $called = false;
	public function test( $request ) {
		return true;
	}
	public function dispatch() {
		$this->called = true;
	}
	public function isCalled( ) {
		return $this->called;
	}
} 