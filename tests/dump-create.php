<?
require_once dirname(__FILE__).'/../_cfg/init.php';
require_once CLASS_PATH . 'dumper/dumper.class.php';
set_time_limit(0);
// СОХРАНЕНИЕ СТАРОЙ БД :) 
$dumper = new CDumper();
$szContent = $dumper->export();
file_put_contents('./original-dump.sql',$szContent);
print 'Database created'."\r\n";
SystemRegisterSample::createCache();
print 'System register cache cleared'."\r\n";
?>