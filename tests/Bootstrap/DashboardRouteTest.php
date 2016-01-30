<?php

namespace Extasy\tests\Bootstrap {
	use Extasy\Request;
	use Faid\DBSimple;
	use Faid\Dispatcher\HttpRoute;
	use \Faid\Request\HttpRequest;
	use \Extasy\Dashboard\Route;
	use \Extasy\tests\Helper;
	use \UsersLogin;

	class DashboardRouteTest extends \Extasy\tests\BaseTest {
		const password = 'password';
		const login    = 'login';

		public function setup() {
			parent::setUp();
			\ACL::create( \CMSAuth::AdministratorRoleName );
			Helper::setupUsers( array(
									array( 'login'    => self::login,
										   'password' => self::password,
										   'rights'   => array( \CMSAuth::AdministratorRoleName => true )
									)
								) );
			$user = \UserAccount::getByLogin( self::login );
			\ACL::grant( \CMSAuth::AdministratorRoleName, $user->rights->getEntity());


		}

		public function testTest() {
			$urlFixture = '/admin/test/';
			$request    = new Request();
			$request->uri( $urlFixture );
			$config = array(
				'controller' => '\\Extasy\\tests\\Bootstrap\\TestController',
				'method'     => 'process',
				'url'        => $urlFixture
			);
			$route  = new Route( $config );
			$this->assertTrue( (bool)$route->test( $request ) );
			$config[ 'url' ] = '/admin/unknown/';
			$route           = new Route( $config );
			$this->assertFalse( (bool)$route->test( $request ) );

		}
	}
}