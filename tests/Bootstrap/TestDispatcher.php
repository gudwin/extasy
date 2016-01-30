<?php
namespace Extasy\tests\Bootstrap {
	use \Faid\Dispatcher\Dispatcher;

	class TestDispatcher extends Dispatcher {
		protected $called = false;
		public function run( ) {
			$this->called = true;
			if ( !empty( $this->routes)) {
				return $this->routes[0];
			} else {
				throw new \NotFoundException('Test route not found');
			}
		}
		public function isCalled( ) {
			return $this->called;
		}
	}
}