<?php
use \Extasy\CMS;
$path = dirname(__FILE__).'/scripts/_lib/loader.php';

if ( file_exists( $path )) {
	require_once $path;
} else {
	die('FileManager. Configure application path');
}
$config = [
    'HttpPrefix' => \Extasy\CMS::getFilesHttpRoot()
];

?><!DOCTYPE html>
<html id="Document">
<head>
	<meta charset="utf-8" />
	<title> </title>
	<link rel="stylesheet" type="text/css" href="style.css" />
    <script type="text/javascript">
        var fmCONSTANTS = <?=json_encode( $config );?>
    </script>
	<script type="text/javascript" src="/resources/extasy/js/vendors/jquery-1.10.2.min.js"></script>
	<script type="text/javascript" src="/resources/extasy/js/net.js"></script>
	<script type="text/javascript" src="/resources/extasy/js/cms/main.js"></script>
	<script type="text/javascript" src="/resources/extasy/js/cms/popup.js"></script>
	<script type="text/javascript" src="/resources/extasy/js/contentloader.js"></script>
	<script type="text/javascript" src="/resources/extasy/js/sysutils.js"></script>
	<script type="text/javascript" src="/resources/extasy/js/controller.js"></script>

	<script type="text/javascript" src="scripts/pathinfo.php"></script>
	<script type="text/javascript" src="js/rights.js"></script>
	<script type="text/javascript" src="js/view.js"></script>
	<script type="text/javascript" src="js/viewer.js"></script>
	<script type="text/javascript" src="js/editor.js"></script>
	<script type="text/javascript" src="js/init.js"></script>

</head>
<body class="PopupBody" id="PopupBody"  onkeydown="return bodyOnKeyDown(event);" onload="init();">
	<table>
		<tr>
			<td>
				<h2 class="PopupHeader" id="path"> </h2>
				<div class="ContentSpacer">
				</div>
					<div class="ContentBlock Form">
						<table id="panel">
							<tr id="rowTop">
								<td style="width:60%"><a href="#" id="columnFile" style="color:#156299">Файл</a></td>
								<td style="width:10%"><a href="#" id="columnSize" style="color:#156299">Размер</a></td>

								<td style="width:10%"><a href="#" id="columnRights" style="color:#156299">Права</a></td>

								<td style="width:5%"><img src="pic/icons/edit.gif"/></td>
								<td style="width:5%"><img src="pic/icons/delete.gif"/></td>
							</tr>
							<tr>
								<td colspan="100" class="HeaderSplitter"><div></div></td>
							</tr>
							<tr>
								<td class="FieldName3" colspan><span style="color:red">Подождите, пока идет загрузка данных </span> </td>
							</tr>
						</table>
					</div>

				<div class="ContentBlock Actions">
					<table class="SubSections">
						<tr>
							<td> Файлы сайта</td>
							<td><a href="#" id="helpButton"> </a></td>
							<!-- <td><a href="#" id="viewButton"> Просмотреть файл </a></td>
							<td><a href="#" id="editButton"> Ред./Перейти </a></td>-->
							<td><a href="#" id="folderButton"> Создать папку </a></td>
							<td><a href="#" id="renameButton"> Переименовать </a></td>
							<td><a href="#" id="deleteButton"> Удалить </a></td>
							<td><a href="#" id="uploadButton"> Закачать файл</a></td>

						</tr>
					</table>
				</div>
			</td>
		</tr>
	</table>
</body>
<div class="popupFrame" id="uploadFrame">
<iframe id="uploadFrameIframe" style="width:300px;height:100px;z-index:501" src="scripts/upload.php"></iframe>
</div>
<div class="popupFrame" id="changerightsFrame">
<table width=300px>
	<tr>
		<td>
<fieldset>
<legend>Владелец</legend>
<input type="checkbox" id="rightsOwnerRead"  value=1>
<label for="rightsOwnerRead">Чтение</label><br>
<input type="checkbox" id="rightsOwnerWrite" value=1>
<label for="rightsOwnerWrite">Запись</label><br>
<input type="checkbox" id="rightsOwnerExecute" value=1>
<label for="rightsOwnerExecute">Вызов</label>
</fieldset>
		</td>
		<td>
<fieldset>
<legend>Группа</legend>
<input type="checkbox" id="rightsGroupRead" value=1>
<label for="rightsGroupRead">Чтение</label><br>
<input type="checkbox" id="rightsGroupWrite" value=1>
<label for="rightsGroupWrite">Запись</label><br>
<input type="checkbox" id="rightsGroupExecute" value=1>
<label for="rightsGrouprExecute">Вызов</label>
</fieldset>
		</td>
		<td>
<fieldset>
<legend>Мир (Остальные)</legend>
<input type="checkbox" id="rightsWorldRead" value=1>
<label for="rightsWorldRead">Чтение</label><br>
<input type="checkbox" id="rightsWorldWrite" value=1>
<label for="rightsWorldWrite">Запись</label><br>
<input type="checkbox" id="rightsWorldExecute" value=1>
<label for="rightsWorldExecute">Вызов</label>
</fieldset>

		</td>
	</tr>
	<tr>
		<td colspan=3>Выбрано - установленный атрибут</td>
	</tr>
	<tr>
		<td colspan=3><input type="text" size=3  id="rightsCalculator"></td>
	</tr>
	<tr>
		<td colspan=2>
			<button id="rightsButtonOK" > OK </button>
			<button id="rightsButtonCancel" > Cancel </button></center>

		</td>
	</tr>

</table>
</div>
<div class="popupFrame" id="viewFrame">
<iframe id="viewFrameIframe" style="width:600px;height:500px;z-index:501" ></iframe>
</div>
<div class="popupFrame" id="editFrame">
<iframe id="editFrameIframe" style="width:600px;height:500px;z-index:501" ></iframe>
</div>

<div class="popupFrame" id="helpFrame">
<iframe src="help/index.html" style="width:600px;height:500px;z-index:501" ></iframe>
</div>
<script>
</script>
</html>
