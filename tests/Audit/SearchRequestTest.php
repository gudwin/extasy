<?php

namespace Extasy\tests\Audit;

use \Extasy\Audit\SearchRequest;
class SearchRequestTest extends base {

	public function testInvalidFixtures( ) {
		$invalidFixtures = array(
			array('page'=> -1),
			array('limit'=> 1001),
			array('limit'=> 0),

			array('date_from' => '2001-01-01 00:00:00'),
			array('date_to' => '2001-01-01 00:00:00'),
			array('date_from' => 'not_an_date'),
			array('date_to' => 'not_an_date'),
			array('date_from' => '2001-01-01 00:00:01','date_to' => '2001-01-01 00:00:00'),
			array('sort_by' => 'unknown_field'),
		);

		foreach ( $invalidFixtures as $row ) {
			$request = new SearchRequest() ;
			foreach ( $row as $field => $value ) {
				$request->$field = $value;
			}
			try {
				$request->validate();
				$this->fail();
			} catch ( \Exception $e ) {
			}
		}
	}
	public function testValidFixture( ) {
		$validFixtures = array(

			array('sort_by' => 'id', 'order' => 'desc'),
			array('sort_by' => 'date', 'order' => 'desc'),
			array('sort_by' => 'date', 'order' => 'asc'),
			array('page'=> 0),
			array('limit'=> 1000),
			array('page'=> 100, 'limit' => 100),
			array('search_phrase'=> 100),

		);
		foreach ( $validFixtures as $key=>$row ) {
			$request = new SearchRequest() ;
			foreach ( $row as $field => $value ) {
				$request->$field = $value;
			}
			try {
				$request->validate();
			} catch ( \Exception $e ) {
				$this->fail();
			}
		}
	}
}