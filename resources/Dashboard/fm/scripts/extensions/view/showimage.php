<?
require_once __DIR__ . DIRECTORY_SEPARATOR . '../../_lib/loader.php';
if (empty($_GET['path'])) {
	die();
}

$szPath = toCanonical(realpath(FILE_PATH.$_GET['path']));
$szErrorMessage = checkpath($szPath);
if (!empty($szErrorMessage)) {
	die($szErrorMessage);
}
header('Content-Type: image/jpeg');
print @file_get_contents($szPath);

?>