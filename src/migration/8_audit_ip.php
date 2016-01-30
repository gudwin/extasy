<?php
use \Faid\DB;

$sql = 'alter table %s modify `ip` varchar( 16 ) not null;';
$sql = sprintf( $sql, \Extasy\Audit\Record::tableName );
DB::post( $sql );