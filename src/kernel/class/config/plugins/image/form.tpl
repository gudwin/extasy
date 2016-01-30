<?
$szButton = _msg('Выбрать изображение через Файловый Менеджер');
$szSize = _msg('Размер изображения');
$szType = _msg('Тип изображения');
$szWidth = _msg('Ширина');
$szHeight = _msg('Высота');
$szHTTP_PATH_TO_FM = '/extasy/Dashboard/fm/index.php';
$szResult = <<<EOD
	<script type="text/javascript">
	function savePathToImage(url) {
		if (config_editor_image != '') {
			document.getElementById('config_editor_image_' + config_editor_image).src = url;
			document.getElementById('config_editor_path_' + config_editor_image).value = url;
		}
	}
	function callFM_Image(szId) {
		config_editor_image = szId;
		fm_callback = savePathToImage;
		window.open('{$szHTTP_PATH_TO_FM}','_blank',',scrollbars=yes,width=700,height=500,left=0,top=0')
	}
	</script>
	<table width="100%" cellpadding="0" cellspacing="0">
		<tr>
			<td width="90%" valign="top">
				<img id="config_editor_image_{$this->szName}" src="{$this->szContent}" alt="">
			</td>
			<td valign="top">
				<nobr><b> {$szSize}</b> : {$aImageSize['size']}</nobr> <br>
				<nobr><b> {$szType}</b> : {$aImageSize['type']}</nobr> <br>
				<nobr><b> {$szWidth}</b> : {$aImageSize['width']}</nobr> <br>
				<nobr><b> {$szHeight}</b> : {$aImageSize['height']}</nobr> <br>
			</td>
		</tr>
	</table>
	<input type="text" id="config_editor_path_{$this->szName}" style="width:400px" name="{$this->szName}" value="{$this->szContent}">
	<input type="button" onclick="callFM_Image('{$this->szName}');return false;" value="{$szButton}">
EOD;
return $szResult;
?>