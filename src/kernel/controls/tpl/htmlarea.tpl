<?
	use \Extasy\CMS;
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<HTML>
<HEAD>
<TITLE> Edit HTML </TITLE>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" >
</HEAD>

<BODY style="padding:0px;margin:0px;">
<form method="post" action="htmlarea.php">
<input type="hidden" name="control" value="htmlarea"/>
<input type="hidden" name="method" value="postHTMLArea"/>
<input type="hidden" name="id" value="<?=$id?>"/>
<textarea id="content" name="content" style="width:800px;height:465px"></textarea>
<script>

	var obj = document.getElementById('content');
	obj.value = window.opener.document.getElementById("<?=$id?>").value;
</script>
<div style="margin-left:30px;margin-top:5px;">
	<input type="submit" value="<?=('Сохранить')?>">
		<!-- <a href="javascript:;" onmousedown="alert(tinyMCE.get('content').getContent());">[Get contents]</a>-->
</div>
</form>
<script language="javascript" type="text/javascript" src="/resources/extasy/tiny_mce/tiny_mce.js"></script>
<script language="javascript" type="text/javascript" src="/resources/extasy/tiny_mce/langs/ru.js"></script>
<script language="javascript" type="text/javascript" src="/resources/extasy/tiny_mce/langs/plugins-ru.js"></script>
<script language="javascript" type="text/javascript">
	tinyMCE.init({
		mode : "textareas",
		language : 'ru',
		relative_urls : false,
		theme : "advanced",
		plugins : "pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,wordcount,advlist,autosave",
		// Theme options
		theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
		theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
		theme_advanced_buttons3 : "tablecontrols,|,hr,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
		theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak,restoredraft",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : true,
//		content_css : "<?=$cssPath?>",

		plugin_insertdate_dateFormat : "%Y-%m-%d",
	    plugin_insertdate_timeFormat : "%H:%M:%S",

		file_browser_callback : "fileBrowserCallBack",
		theme_advanced_resize_horizontal : false,
		nonbreaking_force_tab : true,
		apply_source_formatting : true,
		style_formats : [
			{title : 'Bold text', inline : 'b'},
			{title : 'Red text', inline : 'span', styles : {color : '#ff0000'}},
			{title : 'Red header', block : 'h1', styles : {color : '#ff0000'}},
			{title : 'Example 1', inline : 'span', classes : 'example1'},
			{title : 'Example 2', inline : 'span', classes : 'example2'},
			{title : 'Table styles'},
			{title : 'Table row 1', selector : 'tr', classes : 'tablerow1'}
		]
	});
	var currentMCE = '';
	var currentWIN = '';
	function fileBrowserCallBack(field_name, url, type, win) {

		// Insert new URL, this would normaly be done in a popup
		currentMCE = field_name;
		currentWIN = win
		window.open('/extasy/Dashboard/fm/index.php','_blank','width=800,height=600,left=0,top=0,scrollbars=1');
	}
	function saveBrowserCallBack(url) {
		currentWIN .document.forms[0].elements[currentMCE].value = url;
	}
</script>
</BODY>
</HTML>