<?php
namespace Extasy\Users\Tasks;

use \Faid\DBSimple;

class CleanLoginAttempts {

	public function clean() {
		// cleanup table remove all messages older than month
		DBSimple::delete(
				\UsersLogin::LoginAttemptsTable,
				array(
					'`date` < NOW() - interval 1 month '
				)
		);
	}

	public static function action( $job ) {
		try {
			$job = new CleanLoginAttempts();
			$job->clean();
		}
		catch ( \Exception $e ) {

		}
		finally {
			self::pushTask();
		}
	}

	public static function pushTask() {
		$job              = \Extasy\Schedule\JobFactory::createDelayed( '+1 day' );
		$job->action      = 'action';
		$job->class       = '\\Extasy\\Users\\Tasks\\CleanLoginAttempts';
		$job->dateCreated = date( 'Y-m-d H:i:s' );
		$job->insert();
	}


} 