<?php

namespace Extasy\tests\Audit;

use Extasy\Audit\Log;
use Extasy\Audit\Record;
use Extasy\Audit\SearchRequest;
use Faid\DBSimple;


class RecordTest extends base {

	public function testLogCreatedIfUnknownPassed( ) {
		try {
			$result = Record::add( 'Unknown', '1','2');
		} catch ( \Exception $e ) {
			throw $e;
		}
		$log = Log::getByName( 'Unknown');
		$this->assertTrue( $log instanceof Log);
	}
	public function testDefaultValuesSetCorrectlyAfterAdd( ) {
		$result = Record::add( 'Log1', '1','2');
		$this->assertTrue( $result instanceof Record );
		$this->assertNotEquals( '0000-00-00 00:00:00', $result->date->getValue());
		$this->assertTrue(strlen( $result->ip->getValue()) > 0);
		$this->assertEquals('login', $result->user_login->getValue());;
		$this->assertEquals(1, $result->user_id->getValue());;
	}
	public function testTimeSetCorrectlyInNewRecord( ) {
		$originalTimestamp = time();
		$result = Record::add( 'Log1', '1','2');
		//
		$record = new Record( );
		$record->get( $result->id->getValue() );
		//
		$storedTime = date_create_from_format('Y-m-d H:i:s',$record->date->getValue());
		$storedTimestamp = $storedTime->getTimestamp();

		$this->assertTrue( $storedTimestamp - $originalTimestamp < 2 );
		$this->assertTrue( $originalTimestamp - $storedTimestamp < 2 );
	}
	public function testRecordNotAddedInEnableLoggingDisabled( ) {
		$initial = DBSimple::getRowsCount( Record::tableName );
		//
		$log = Log::getByName('Log1');
		$log->setupLogging( false );
		//
		$result = Record::add( 'Log1', '1','2');
		$this->assertTrue( empty( $result ));
		//
		$result =  DBSimple::getRowsCount( Record::tableName );
		$this->AssertEquals( $initial, $result );
	}
	public function testSearchByUserName( ) {
		$request = new SearchRequest();
		$request->user = 'login';
		$result = Record::select( $request );
		$this->assertEquals( 2, sizeof( $result ) );
		$this->assertEquals( 2,  $result[0]['record']->id->getValue());
		$this->assertEquals( 1,  $result[1]['record']->id->getValue());
	}
	public function testSearchByUserNameOrdered( ) {
		$request = new SearchRequest();
		$request->user = 'login';
		$request->order = SearchRequest::OrderAsc;
		$result = Record::select( $request );
		$this->assertEquals( 2, sizeof( $result ) );
		$this->assertEquals( 1,  $result[0]['record']->id->getValue());
		$this->assertEquals( 2,  $result[1]['record']->id->getValue());
	}
	public function testOrderByEventName( ) {
		$request = new SearchRequest();
		$request->sort_by = 'event';
		$result = Record::select( $request );
		$this->assertEquals( 3, sizeof( $result ) );
		$this->assertEquals( 2, $result[0]['record']->id->getValue());
	}
	public function testSearchByText( ) {
		$request = new SearchRequest();
		$request->search_phrase= 'short';
		$result = Record::select( $request );
		$this->assertEquals( 3, sizeof( $result ) );
		$request = new SearchRequest();
		$request->search_phrase= 'unknown';
		$result = Record::select( $request );
		$this->assertEquals( 0, sizeof( $result ) );
	}

}
