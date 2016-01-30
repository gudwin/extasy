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
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<HEAD>
<TITLE> Viewing file [<?=htmlspecialchars($_GET['path'])?>]</TITLE>
<META NAME="Generator" CONTENT="EditPlus">
<META NAME="Author" CONTENT="">
<META NAME="Keywords" CONTENT="">
<META NAME="Description" CONTENT="">
</HEAD>

<BODY>
<pre><?=htmlspecialchars(file_get_contents($szPath))?></pre>
</BODY>
</HTML>
