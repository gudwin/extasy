
<?php
use \Faid\DB;
use \Faid\DBSimple;
use \Extasy\Audit\Record;
use \Extasy\Audit\Log;

DB::post('TRUNCATE audit_logs');
DB::post('TRUNCATE audit_records');
//
$sql = 'select distinct category from cms_log order by category asc';

$data = DB::query($sql);

foreach ( $data as $row ) {
	$log = createLog( $row );
	importMessages( $log, $row['category']);

}
function createLog( $row ) {
	$log = new Log();
	$log->name = 'Developer.'.$row['category'];
	$log->enable_logging = true;
	if ( CMSLog::RuntimeErrors == $row['category']) {
		$log->critical = true;
	}
	$log->insert();
	return $log;
}
function importMessages( $log, $category  ) {
	$data = selectMessages( $category   );
	foreach ( $data as $record ) {
		Record::add( $log->name, $record['message'], $record['message']);
	}
}
function selectMessages( $category ) {
	return DBSimple::select( 'cms_log', array(
		'category' => $category
	),'id asc');
}