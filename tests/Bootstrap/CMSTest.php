<?php
namespace Extasy\tests\Bootstrap {
	use \Faid\Dispatcher\HttpRoute;
	use \Faid\Request\HttpRequest;
	use \Extasy\tests\BaseTest;
	use \Extasy\CMS;

	class CMSTest extends BaseTest {

		public function testIfDispatcherCalled( ) {
			$request = new TestRequest();
			$route = new TestRoute(array(
				'url' => '/test_url'
								   ));
			//
			$myDispatcher = new TestDispatcher( $request );
			$myDispatcher->addRoute( $route );
			$cms = new CMS( $myDispatcher );
			$cms->dispatch();

			$this->assertTrue( $myDispatcher->isCalled() );
			$this->assertTrue( $route->isCalled() );
		}
		public function testGetActiveRoute( ) {
			$request = new TestRequest();
			$route = new TestRoute(array(
				'url' => '/test_route'
								   ));
			//
			$myDispatcher = new TestDispatcher( $request );
			$myDispatcher->addRoute( $route );
			$cms = new CMS( $myDispatcher );
			$cms->dispatch();

			$active = $cms->getActiveRoute( );
			$this->assertEquals( $route, $active );
		}


	}
}