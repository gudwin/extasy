<?php
namespace Extasy\Audit;

use \Extasy\Api\Exception;
use \Extasy\Model\Model as extasyDocument;
use \Faid\DBSimple;
use \UsersLogin;
use \Faid\DB;
use \Email_Controller;
use \CConfig;

class Record extends extasyDocument {
	const ModelName = '\\Extasy\\Audit\\Record';

	const tableName = 'audit_records';

	const CriticalEmailSchemaName = 'Audit.CriticalEventName';

	/**
	 * @var Log
	 */
	protected $log = null;

	/**
	 * @var SearchRequest
	 */
	protected static $lastSearchRequest;


	public function view() {
		if ( $this->viewed->getValue() == 0 ) {
			$this->viewed->setValue( 1 );
			$this->update();
		}
	}

	public function getParseData() {
		$result = array(
			'id'         => $this->id->getValue(),
			'short'      => nl2br( $this->short->getValue() ),
			'full'       => nl2br( $this->full->getValue() ),
			'ip'         => $this->ip->getValue(),
			'date'       => $this->date->getValue(),
			'user_id'    => $this->user_id->getValue(),
			'user_login' => $this->user_login->getValue(),
			'viewed'     => $this->viewed->getValue(),
		);

		return $result;
	}

	public function insert() {
		parent::insert();
		if ( empty( $this->log ) ) {
			if ( !$this->log_id->getValue() ) {
				throw new \NotFoundException( 'Log record not set. Record can`t be added ' );
			}
			$this->log = new Log();
			$this->log->get( $this->log_id->getValue() );
		}
		if ( $this->log->critical->getValue() ) {
			$this->sendEmailNotification();
		}
	}

	public function setLog( $log ) {
		$this->log = $log;
		$this->log_id->setValue( $log->id->getValue() );
	}

	public static function add( $logName, $short, $full = null ) {
		$log = Log::createIfNotExists( $logName );
		if ( !$log->enable_logging->getValue() ) {
			return null;
		}
		if ( empty( $full ) ) {
			$full = $short;
		}
		if ( $short instanceof \Exception ) {
			$short = $short->getMessage();
		}
		if ( $full instanceof \Exception ) {
			$full = (string)$full;
		}
		//
		$result = new Record();
		$result->setLog( $log );
		self::fillUserInfo( $result );
		$result->short->setValue( $short );
		$result->full->setValue( $full );
		//
		$result->insert();


		return $result;
	}

	public static function getNewCount() {
		return DBSimple::getRowsCount( Record::tableName, array( 'viewed' => 0 ) );
	}

	public static function select( SearchRequest $request ) {
		selF::$lastSearchRequest = $request;

		$request->validate();
		$sql = <<<SQL
	select SQL_CALC_FOUND_ROWS a.*,l.name as event
	from %s as a
	inner join %s as l
	on a.log_id = l.id
	where %s
	order by %s %s
	limit %d,%d
SQL;
		$sql = sprintf( $sql,
						Record::tableName,
						Log::tableName,
						self::buildSQLSelectCondition(),
						$request->sort_by,
						$request->order,
						$request->page * $request->limit,
						$request->limit );

		$data = DB::query( $sql );

		$result = array();

		foreach ( $data as $row ) {
			$result[ ] = array(
				'record'    => new Record( $row ),
				'eventInfo' => array(
					'event' => $row[ 'event' ]
				)
			);
		}

		return $result;
	}

	protected static function buildSQLSelectCondition() {
		$parts = array();

		if ( !empty( self::$lastSearchRequest->search_phrase ) ) {
			$parts[ ] = sprintf(
				'( `short` like "%%%s%%" or `full` like "%%%s%%"  )',
				to_search_string( self::$lastSearchRequest->search_phrase ),
				to_search_string( self::$lastSearchRequest->search_phrase )
			);
		}
		$emptyDate        = '0000-00-00 00:00:00';
		$validateDateFrom = !empty( self::$lastSearchRequest->date_from ) && ( $emptyDate != self::$lastSearchRequest->date_from );
		if ( $validateDateFrom ) {
			$parts[ ] = sprintf( 'a.date >= "%s"', DB::escape( selF::$lastSearchRequest->date_from ) );
		}
		$validateDateTo = !empty( self::$lastSearchRequest->date_to ) && ( $emptyDate != self::$lastSearchRequest->date_to );
		if ( $validateDateTo ) {
			$parts[ ] = sprintf( 'a.date <= "%s"', DB::escape( selF::$lastSearchRequest->date_to ) );
		}
		if ( !empty( self::$lastSearchRequest->user ) ) {
			$parts[ ] = sprintf( ' a.user_login = "%s"', DB::escape( self::$lastSearchRequest->user ) );
		}
		if ( !empty( $parts ) ) {
			return implode( ' and ', $parts );
		} else {
			return ' 1 ';
		}

	}

	public static function getPagingInfo() {
		$sql    = 'select FOUND_ROWS() as `found` ';
		$result = DB::get( $sql );
		$found  = !empty( $result[ 'found' ] ) ? intval( $result[ 'found' ] ) : 0;

		return array(
			'page'  => self::$lastSearchRequest->page,
			'total' => $found
		);
	}

	public static function getFieldsInfo() {
		return array(
			'table'  => self::tableName,
			'fields' => array(
				'id'         => '\\Extasy\\Columns\\Index',
				'log_id'     => '\\Extasy\\Columns\\Number',
				'short'      => '\\Extasy\\Columns\\Text',
				'full'       => '\\Extasy\\Columns\\Text',
				'ip'         => '\\Extasy\\Columns\\IP',
				'date'       => '\\Extasy\\Columns\\Datetime',
				'user_id'    => '\\Extasy\\Users\\Columns\\Username',
				'user_login' => '\\Extasy\\Columns\\Input',
				'viewed'     => '\\Extasy\\Columns\\Boolean',
			)
		);
	}

	protected static function fillUserInfo( $record ) {
		if ( UsersLogin::isLogined() ) {
			try {
				$user = UsersLogin::getCurrentSession();
				$record->user_id->setValue( $user->id->getValue() );
				$record->user_login->setValue( $user->login->getValue() );
			}
			catch ( \NotFoundException $e ) {

			}

		} else {
		}

		return $record;
	}

	protected function sendEmailNotification() {
		try {
			$config    = CConfig::getSchema( self::CriticalEmailSchemaName );
			$values    = $config->getValues();
			$parseData = array(
				'record' => $this->getParseData(),
				'log'    => $this->log->getParseData()
			);
			Email_Controller::parseAndSend( $values[ 'to' ], $values[ 'subject' ], $values[ 'content' ], $parseData );
		}
		catch ( \Exception $e ) {
		}
	}
}
