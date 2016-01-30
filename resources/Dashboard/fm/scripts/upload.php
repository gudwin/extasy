<?
use \Extasy\CMS;
require_once dirname(__FILE__).'/_lib/loader.php';

$fs = DAO::getInstance('fs');

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML id="document">
<?
if (isset($_POST['basepath']) && (!empty($_FILES['file'])) && (is_uploaded_file($_FILES['file']['tmp_name']))) {
	$szPath = toCanonical(realpath(FILE_PATH.$_POST['basepath']));
	$szErrorMessage = checkPath($szPath );
	if (empty($szErrorMessage)) {
		$fs->upload('file',$szPath.str_replace(' ','',$_FILES['file']['name']));
		$fs->chmod($szPath.str_replace(' ','',$_FILES['file']['name']),0777)
		?>
<HEAD>
<TITLE> New Document </TITLE>
<META NAME="Generator" CONTENT="EditPlus">
<META NAME="Author" CONTENT="">
<META NAME="Keywords" CONTENT="">
<META NAME="Description" CONTENT="">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</HEAD>
<BODY BGCOLOR=#EEEEEE >
	<script>
	if (window.parent.ii != null)
	{
		window.parent.ii.inClosePopup();
	}
	
	</script>
</BODY>

	<?
	}
	
} else {
?>

<HEAD>
<TITLE> New Document </TITLE>
<META NAME="Generator" CONTENT="EditPlus">
<META NAME="Author" CONTENT="">
<META NAME="Keywords" CONTENT="">
<META NAME="Description" CONTENT="">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</HEAD>
	<script type="text/javascript" src="/resources/js/vendors/jquery-1.10.2.min.js"></script>
	<script type="text/javascript" src="/resources/extasy/js/net.js"></script>
	<script type="text/javascript" src="/resources/extasy/js/cms/main.js"></script>
	<script type="text/javascript" src="/resources/extasy/js/contentloader.js"></script>
	<script type="text/javascript" src="/resources/extasy/js/sysutils.js"></script>
	<script type="text/javascript" src="/resources/extasy/js/controller.js"></script>


<BODY BGCOLOR=#EEEEEE >
<form method="post" enctype="multipart/form-data" action="upload.php" >
<input type="file" name="file">
<input type="hidden" name="basepath" id="basepath" value=''>
<input type="submit" value="Закачать" style="z-index:502">

</form>
<script>
if (window.parent.ii != null)
{
	
	document.getElementById('basepath').value = window.parent.ii.szPath;
}

</script>

</BODY>

<?
}
?>

</HTML>