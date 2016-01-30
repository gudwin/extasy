<?
// Extasy Framework
// by Gisma (info@gisma.ru)
// Хранит функции работы с массивами, файлами, датами и т.д.
// ***************************************************************
if (!defined('ADMIN_EMAIL')) { define('ADMIN_EMAIL','dmitrey.schevchenko@gmail.com'); }
// Объявляем версию ядра
define('EXTASY_VERSION','4.2 - ');
// Опередялем вдруг мы CLI-моде

if (php_sapi_name() == 'cli')
{
	define('CLI_MODE','1');
} else {
}
if ( !headers_sent() ) {
	session_start();
}


// Грузим обязательные пути
define('CLASS_PATH',LIB_PATH.'kernel/class/');
define('CONTROL_PATH',LIB_PATH.'kernel/controls/');
// Load faid
require_once LIB_PATH . 'kernel/faid-0.5.php';
require_once LIB_PATH . 'kernel/faid-page.php';
require_once LIB_PATH . 'kernel/functions/strings.func.php';
// Директивы Kernel
define('RESOURCES_PATH',WEBROOT_PATH.'resources/');

include LIB_PATH . 'kernel/autoload.php';

function _debug( ) {
	\Faid\Debug\displayCallerCode(1);
	$data = func_get_args();
	foreach ($data as $key=>$row ) {
		var_dump( $row );
	}
	die();
}

?>