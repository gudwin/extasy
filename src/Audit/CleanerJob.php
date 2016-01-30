<?php

namespace Extasy\Audit;


class CleanerJob extends \Extasy\Schedule\Job {
	protected function action() {
		try {
			Cleaner::pack();
		} catch (\Exception $e ) {
			\Extasy\Audit\Record::add( __CLASS__, $e->getMessage(), $e );
		}
		$job = new CleanerJob();
		$job->actionDate->setTime('+1 hour');
		$job->insert();
	}
} 