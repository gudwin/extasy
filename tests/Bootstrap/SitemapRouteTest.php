<?php

namespace Extasy\tests\Bootstrap {
	use Faid\DBSimple;
	use \Faid\Request\HttpRequest;
	use \Extasy\sitemap\Route;
	use \Extasy\tests\Helper;
	use \Faid\DB;

	class SitemapRouteTest extends \Extasy\tests\BaseTest {
		const urlFixture       = '/test_url/';
		const scriptUrlFixture = '/test_script/';
		const scriptPath       = 'tests/Bootstrap/TestScript.php';
		protected static $sitemapScriptCalledFlag = false;

		public function setUp() {
			parent::setUp();
			Helper::dbFixture( SITEMAP_TABLE,
							   array(
								   array(
									   'name'          => 'sitemap',
									   'document_name' => Model::ModelName,
									   'document_id'   => 1,
									   'full_url'      => '//' . self::urlFixture
								   ),
								   array(
									   'name'     => 'sitemap2',
									   'script'   => self::scriptPath,
									   'full_url' => '//' . self::scriptUrlFixture
								   ),
							   )
			);

			$this->dropTable();
			$this->createTable();

			self::$sitemapScriptCalledFlag = false;
		}

		public function tearDown() {
			$this->dropTable();
			//
			parent::tearDown();
		}

		protected function dropTable() {
			$sql = 'drop table if exists %s  ';
			$sql = sprintf( $sql, Model::TableName );
			DB::post( $sql );

		}

		protected function createTable() {
			$sql = <<<SQL
create table `test_document` (
	`id` int not null auto_increment,
	`name` varchar(255 ) not null default '',
	primary key (`id`)
);
SQL;
			DB::post( $sql );
			DBSimple::insert( Model::TableName, array( 'name' => 'sitemap document' ) );
		}

		public function testUnknownUrl() {
			$request = new HttpRequest();
			$request->uri( '/unknown_url' );
			$route = new Route( array( 'url' => '//'.self::urlFixture ) );
			$this->assertFalse( $route->test( $request ) );

		}

		/**
		 * @expectedException \ForbiddenException
		 */
		public function testDispatchNotValidRoute() {
			$route = new Route( array( 'url' => '//'.self::urlFixture ) );
			$route->dispatch();
		}

		public function testUrl() {
			$route   = new Route();
			$request = new HttpRequest();
			$request->uri( self::urlFixture );
			$this->assertTrue( $route->test( $request ) );
		}

		public function testDispatchDocument() {
			$route = new Route();

			$request = new HttpRequest();
			$request->uri( self::urlFixture );

			$this->assertTrue( $route->test( $request ) );

			$this->assertFalse( Model_Controller::isCalled() );
			$route->dispatch();
			$this->assertTrue( Model_Controller::isCalled() );
		}

		public function testDispatchScript() {
			$route = new Route();

			$request = new HttpRequest();
			$request->uri( self::scriptUrlFixture );

			$this->assertTrue( $route->test( $request ) );
			$this->assertFalse( self::$sitemapScriptCalledFlag );
			$route->dispatch();
			$this->assertTrue( self::$sitemapScriptCalledFlag );

		}

		public static function sitemapScriptCalled() {
			self::$sitemapScriptCalledFlag = true;
		}

	}
}