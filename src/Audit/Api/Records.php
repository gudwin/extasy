<?php

namespace Extasy\Audit\Api;

use Extasy\Audit\SearchRequest;
use Extasy\Audit\Record;

class  Records extends ApiOperation {
	const MethodName = 'Audit.Records';
	protected $optionalParams = array(
		'sort_by',
		'order',
		'search_phrase',
		'user',
		'page',
		'limit',
		'date_from',
		'date_to'
	);

	protected function action() {
		$request = $this->buildRequest( );
		$data       = Record::select($request);
		$pagingInfo = Record::getPagingInfo();

		$result = array(
			'list' => array(),
			'total' => $pagingInfo[ 'total' ],
			'page'  => $pagingInfo[ 'page' ],
		);
		foreach ($data as $row) {
			$result[ 'list' ][ ] = $this->packForResponse($row);
		}

		return $result;
	}

	protected function packForResponse($row) {
		$record = $row[ 'record' ]->getParseData();
		$row['record']->view();
		$record['event'] = $row[ 'eventInfo' ][ 'event' ];
		return $record;
	}
	protected function buildRequest( ) {
		$request = new SearchRequest();
		foreach ($this->optionalParams as $row) {
			$value = $this->getParam($row, NULL);
			if ( !is_null($value) ) {
				$request->$row = $value;
			}
		}
		return $request;
	}

}