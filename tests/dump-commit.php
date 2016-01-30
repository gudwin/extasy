<?
require_once dirname(__FILE__).'/../_cfg/mysql_connect.php';
require_once dirname(__FILE__).'/../3.2/class/dumper/dumper.class.php';
set_time_limit(0);

$dumper = new CDumper();
$szContent = file_get_contents('./original-dump.sql');
$dumper->import($szContent);

print 'Database restored'."\r\n";
$path = dirname(__FILE__).'/../_cache/system_register_get';
if (file_exists($path)) {
	unlink($path); 
}
$path = dirname(__FILE__).'/../_cache/system_register_child';
if (file_exists($path)) {
	unlink($path); 
}


print 'System Register cache cleared'."\r\n"
?>