<?php
use \Faid\DBSimple;
use \Extasy\Schedule\Job;

DBSimple::delete( Job::TableName, array(
	'class' => '\\Extasy\\Users\\Tasks\\CleanLoginAttempts',
));
$model = new \Extasy\Users\login\LoginAttempt();
$model->createDatabaseTable( true );
