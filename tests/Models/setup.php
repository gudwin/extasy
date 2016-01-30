<?php
namespace Extasy\tests\Models {
	use \Faid\DB;

	DB::post( 'DROP TABLE IF EXISTS `test_model`  ' );
	$sql = <<<SQL

		CREATE TABLE `test_model` (
		 	id int not null auto_increment,
		 	name varchar(255) not null,
		 	primary key (`id` )
		)

SQL;

	DB::post( $sql );
}