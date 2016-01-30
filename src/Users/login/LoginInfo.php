<?php

namespace Extasy\Users\login;

use \Faid\DB;
use \Faid\DBSimple;
use \Extasy\Users\login\LoginAttempt;

class LoginInfo {
	protected $successId = null;
	protected $successIp = null;
	protected $successDate = null;
	protected $successMethod = null;


	protected $failCount = null;
	protected $failLastIp = null;
	protected $failLastDate = null;




	protected static $session = null;

	protected $user = null;

	public function getViewData() {
		return array(
			'success' => array(
				'ip'     => $this->successIp,
				'date'   => $this->successDate,
				'method' => $this->successMethod,
			),
			'fail'    => array(
				'ip'    => $this->failLastIp,
				'date'  => $this->failLastDate,
				'count' => $this->failCount,
			)
		);
	}

	public function countForUser( $user ) {
		$this->user = $user;

		$this->loadLastSuccess( );

		$this->loadLastFail();

	}
	protected function loadLastSuccess() {
		$condition = array(
			'user_id' => $this->user->id->getValue(),
			'status' => LoginAttempt::SuccessStatus,
		);
		$current = DBSimple::get( LoginAttempt::TableName, $condition,'order by `id` desc');
		if ( empty( $current )) {
			throw new \RuntimeException('Can`t load login information.');
		}
		$condition[] = sprintf('id < "%d"', $current['id']);
		$last = DBSimple::get( LoginAttempt::TableName, $condition, 'order by `id` desc');

		if ( !empty( $last )) {
			$model = new LoginAttempt( $last );
			$this->successIp = $model->host->getValue();
			$this->successMethod = $model->method->getValue();
			$this->successDate = $model->date->getValue();
			$this->successId = $model->id->getValue();
		}
	}
	protected function loadLastFail() {
		$lastId = !empty( $this->successId ) ? $this->successId : 0;

		$condition = array(
			'user_id' => $this->user->id->getValue(),
			'status' => LoginAttempt::FailStatus,
			sprintf('`id` > "%d"', DB::escape($lastId) ),
			sprintf('`date` < NOW() ')
		);
		$this->failCount = DBSimple::getRowsCount( LoginAttempt::TableName, $condition );
		$last = DBSimple::get( LoginAttempt::TableName, $condition,'order by `id` desc');
		if ( !empty( $last )) {
			$model = new LoginAttempt( $last );
			$this->failLastIp = $model->host->getValue();
			$this->failLastDate = $model->date->getValue();
		}
	}
	public static function cleanupSession() {
		unset( $_SESSION[ __CLASS__ ] );
	}

	/**
	 * @return \Extasy\Users\login\LoginInfo
	 * @throws \RuntimeException
	 */
	public static function getFromSession() {
		if ( empty( $_SESSION[ __CLASS__ ] ) ) {
			throw new \RuntimeException( 'Session key empty' );
		}
		$result = unserialize( $_SESSION[ __CLASS__ ] );
		return $result;
	}

	public static function setupSession( $user ) {
		$info = new LoginInfo();
		$info->countForUser( $user );
		$_SESSION[ __CLASS__ ] = serialize( $info );
	}
} 