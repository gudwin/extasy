<?
$szHTTP_PATH_TO_FM = \Extasy\CMS::getDashboardWWWRoot().'fm/';
$szTitle = _msg('Выбрать файл через Файловый Менеджер');
$szResult = <<<EOD
	<script type="text/javascript">
	function savePathToFile(url) {
		if (config_editor_file != '') {
			document.getElementById('config_editor_path_' + config_editor_file).value = url;
		}
	}
	function callFM_File(szId) {
		config_editor_file = szId;
		fm_callback = savePathToFile;
		window.open('{$szHTTP_PATH_TO_FM}','_blank','width=700,height=500,left=0,top=0,scrollbars=yes')
	}
	</script>
	<input type="text" id="config_editor_path_{$this->szName}" style="width:400px" name="{$this->szName}" value="{$this->szContent}">
	<input type="button" onclick="callFM_File('{$this->szName}');return false;" value="$szTitle">
EOD;
return $szResult;
?>