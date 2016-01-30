<?php

use \Faid\DB;

$sql = <<<SQL
	create table ddos_detector (
		`id` int not null auto_increment,
		`ip` binary(16) not null default "",
		`date` datetime not null,
		index `search_by_ip` (`ip`),
		primary key (`id` )
	)
SQL;

DB::post( $sql );

SystemRegisterHelper::import( new SystemRegister( '/System/Security/' ),
							  array(
								  'DDosDetector' => array(
									  'MaxConnections' => 100,
									  'Period'         => '1 minute',
									  'Message' => 'Ваш аккаунт был временно заблокирован по причине превышения допустимого количества запросов'
								  )
							  ) );
SystemRegisterSample::createCache();
