<?php

namespace Extasy\tests\Audit;

use Extasy\Audit\Api\Records;

class RecordsApiTest extends base {

	/**
	 * @expectedException \ForbiddenException
	 */
	public function testExceptionThrownOnInvalidRequest() {
		$api = new Records();
		$api->setParamsData(
			array(
				'page' => -1
			)
		);
		$api->exec();
	}

	public function testSearch() {
		$api = new Records();
		$api->setParamsData(
			array(
				'page'  => 1,
				'limit' => 1
			)
		);
		$result = $api->exec();
		$this->AssertTrue( is_array( $result ) );
		$this->assertArrayHasKey( 'list', $result );
		$this->assertArrayHasKey( 'page', $result );
		$this->assertArrayHasKey( 'total', $result );
		$this->assertEquals( 1, $result[ 'page' ] );
		$this->assertEquals( 3, $result[ 'total' ] );
		$this->assertEquals( 1, sizeof( $result[ 'list' ] ) );
		$this->assertEquals( 2, $result[ 'list' ][ 0 ][ 'id' ] );
		//
		$api    = new Records();
		$result = $api->exec();
		$this->assertEquals( 3, sizeof( $result[ 'list' ] ) );
	}

	public function testSearchByUser() {
		$api = new Records();
		$api->setParamsData(
			array(
				'user' => 'login'
			)
		);
		$result = $api->exec();
		$this->AssertEquals( 2, sizeof( $result[ 'list' ] ) );
	}

	public function testViewed() {
		$api    = new Records();
		$result = $api->exec();
		$this->assertEquals( false, $result[ 'list' ][ 0 ][ 'viewed' ] );
		//
		$result = $api->exec();
		$this->assertEquals( true, $result[ 'list' ][ 0 ][ 'viewed' ] );
	}

	public function testSearchByDate() {
		$api = new Records();
		$api->setParamsData(
			array(
				'date_from' => '2001-01-01 00:00:00',
				'date_to'   => '2001-01-01 00:00:00',
			)
		);
		$result = $api->exec();
		$this->AssertEquals( 1, sizeof( $result[ 'list' ] ) );
		//
		$api = new Records();
		$api->setParamsData(
			array(
				'date_from' => '2001-01-01 00:00:00',
				'date_to'   => '2001-01-02 00:00:00',
			)
		);
		$result = $api->exec();
		$this->AssertEquals( 2, sizeof( $result[ 'list' ] ) );
	}

	public function testSearchByPhrase() {
		$api = new Records();
		$api->setParamsData(
			array(
				'search_phrase' => 'short'
			)
		);
		$result = $api->exec();
		$this->AssertEquals( 3, sizeof( $result ) );
	}

}