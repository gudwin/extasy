<?
require_once __DIR__ . DIRECTORY_SEPARATOR . '../../_lib/loader.php';

$szAlert = '';
if (sizeof($_POST) > 0) {
	
	if (empty($_POST['path'])) {
		die();
	}
	$szPath = toCanonical(realpath(FILE_PATH.$_GET['path']));
	$szAlert = checkpath($szPath);
	if (!empty($szAlert)) {
		die($szAlert);
	}
	if (is_writeable($szPath)) {
		$f_header = fopen($szPath,'w+');
		fputs($f_header,$_POST['content']);
		fclose($f_header);
		$szAlert ='Данные изменены';
	} else {
		$szAlert ='Файл защищен от записи';
	}
	
	$szHTTP_PATH = $_POST['path'];
} else {
	if (empty($_GET['path'])) {
		die();
	}

	$szPath = toCanonical(realpath(FILE_PATH.$_GET['path']));
	$szAlert = checkpath($szPath);
	if (!empty($szAlert)) {
		die($szAlert);
	}
	$szHTTP_PATH = $_GET['path'];
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<HEAD>
<TITLE> Viewing file [<?=htmlspecialchars($szHTTP_PATH['path'])?>]</TITLE>
<META NAME="Generator" CONTENT="EditPlus">
<META NAME="Author" CONTENT="">
<META NAME="Keywords" CONTENT="">
<META NAME="Description" CONTENT="">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script type="text/javascript" src="../../../../../resources/extasy/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="../../../../../resources/extasy/js/compiled/latest_release.js"></script>
</HEAD>

<BODY>
<? 
if (!empty($szAlert)) {
?>
<script>
alert("<?=$szAlert?>");
</script>
<?
}
?>
<form method="post" id="form_send">
<input type="submit" value="Обновить">
<hr>
<textarea name="content" id="content" style="width:100%;height:370px;	"><?=htmlspecialchars(file_get_contents($szPath))?></textarea>
<input type="hidden" name="path" value="<?=$szHTTP_PATH['path']?>">
<hr>
<input type="submit"  value="Обновить">
</form>
<script>
	el = 
	{
		keyCode: 13,
		shiftKey: false,
		altKey: false,
		ctrlKey: true
	}
	controller.onKeyDown('form_send',el,'document.getElementById("form_send").submit();return false;');
	document.getElementById('content').focus();
</script>
</BODY>
</HTML>
