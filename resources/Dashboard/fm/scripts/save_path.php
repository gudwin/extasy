<?
require_once dirname(__FILE__).'/_lib/loader.php';

if (!isset($_GET['path'])) {
	die();
}
$szScript = $_SERVER['SCRIPT_NAME'] | $_SERVER['PHP_SELF'];
// Устанавливаем куки
setcookie('fm_path',$_GET['path'],time() + 86400 * 30,'/')
?>