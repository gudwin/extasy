<?php
use \Faid\DB;
$register = new SystemRegister('/System/');
$import = array(
	'Audit' => array(
		'notification_emails' => 'dmitry@dd-team.org'
	),
);

SystemRegisterHelper::import( $register, $import );
SystemRegisterSample::createCache( );
$sql = <<<SQL
	create table `audit_logs` (
		`id` int not null auto_increment,
		`name` varchar(40) not null,
		`description` text null,
		`critical` bool not null default true,
		`enable_logging` bool not null default false,
		PRIMARY key (`id`)
	) DEFAULT CHARSET utf8
SQL;

DB::post( $sql );

$sql = <<<SQL
	create table `audit_records` (
		`id` int not null auto_increment,
		`log_id` int not null,
		`date` datetime default '0000-00-00 00:00:00',
		`user_id` int not null default 0,
		`user_login`  varchar( 40 ) null,
		`short` text null,
		`full` text null,
		`ip` binary(16),
		`viewed` BOOL not null default false
		primary key ( `id` ),
		index search_by_log (`log_id` ),
		index search_by_user_name (`user_login` ),
		index search_by_user (`user_id` ),
		index search_by_datetime ( `date` )
	) DEFAULT CHARSET utf8;

SQL;

DB::post( $sql );