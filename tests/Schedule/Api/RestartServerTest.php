<?php

namespace Extasy\tests\Schedule\Api;


use Extasy\Audit\Record;
use Extasy\Schedule\Api\RestartServer;
use Extasy\tests\Schedule\TestAction;
use Faid\Configure\Configure;
use Faid\DBSimple;

class RestartServerTest extends \Extasy\tests\Schedule\BaseTest {
	protected static $called = false;

	//
	public function setUp() {
		self::$called = false;
		parent::setUp();
	}

	//
	public static function scriptCall() {
		self::$called = true;
	}

	//
	public function testScriptLoaded() {
		Configure::write( RestartServer::ConfigureKey, __DIR__ . '/4restart_server.php');
		//
		$api = new RestartServer();
		$api->exec();
		$this->assertTrue( self::$called );
	}

	//
	public function testTableCleaned() {
		$job = new TestAction();
		$job->insert();
		//
		$api = new RestartServer();
		$api->exec();
		//
		$this->assertEquals( 0, DBSimple::getRowsCount( TestAction::TableName ));

	}

	//
	public function testAuditRecordCreated() {
		$fixture = DBSimple::getRowsCount( Record::tableName );
		//
		$api = new RestartServer();
		$api->exec();
		//
		$this->assertEquals( $fixture + 1, DBSimple::getRowsCount( Record::tableName ));
	}
} 