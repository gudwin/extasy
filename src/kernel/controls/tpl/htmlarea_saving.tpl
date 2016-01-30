<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<HEAD>
<TITLE> Saving HTML </TITLE>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</HEAD>

<BODY>
<script>
	window.opener.document.getElementById('<?=$id?>').value = <?=$content?>;
	if (window.opener.document.getElementById('<?=$id?>').callback) {
		window.opener.document.getElementById('<?=$id?>').callback(window.opener.document.getElementById('<?=$id?>').value);
	}
	window.close();
</script>
</BODY>
</HTML>
