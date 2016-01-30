<?php
use \Faid\DB;


$sql = <<<SQL
	create table schedule_job (
		`id` int not null auto_increment,
		`status` int not null default 0,
		`action` varchar(255) not null default '',
		`class` varchar(255) not null default '',
		`dateCreated` datetime not null default "0000-00-00 00:00:00",
		`actionDate` datetime not null default "0000-00-00 00:00:00",
		`repeatable` int not null default 0,
		`period` int not null default 0,
		`data` blob null,
		primary key (`id`),
		index status (`status`)
	)
SQL;

DB::post( $sql );