<?php
namespace Extasy\Dashboard {
	use \CMSAuth;

	class LoginRoute extends \Faid\Dispatcher\HttpRoute {
		public function __construct( $config = array() ) {

			parent::__construct( $config );
		}

		public function test( $request ) {
			if ( !CMSAuth::getInstance()->isLogined() ) {
				return parent::test( $request );
			}
			return false;
		}
		public function dispatch() {
		}
	}
}