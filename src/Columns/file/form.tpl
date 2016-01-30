<!-- BEGIN form -->
<script>
	if (window.$setFileControlValue == null)
	{
		window.$setFileControlValue = function($value) {
			document.getElementById(window.$FileControlId).value = $value;
			document.getElementById(window.$FileControlId + '_div').innerHTML = 'Путь : ' + $value;
		}
	}
</script>
<table>
	<tr>
		<td>
			Чтобы выбрать файл, нажмите на кнопку
		</td>
		<td>
			<input type=button onclick="
			window.$FileControlId = 'control_file_<?=$name?>_<?=$id?>';
			window.open('<?=\Extasy\CMS::getResourcesUrl()?>extasy/Dashboard/fm/index.php','_blank','scrollbars=yes,width=1024,height=768,left=0,top=0,toolbar=no,titlebar = no, status = no, resizeable = no, menubar = no,location = no, directories = no, fullscreen = yes');return false;" value="Выбрать файл">
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<div id="control_file_<?=$name?>_<?=$id?>_div"> Путь : <?=$value?> <a href="<?php print urldecode($value)?>">Скачать</a></div>
			<a href="#" onclick="
			document.getElementById('control_file_<?=$name?>_<?=$id?>_div').innerHTML = 'Путь : ';
			document.getElementById('control_file_<?=$name?>_<?=$id?>').value = '';
			return false;
			">Удалить</a>

		</td>
		<input type="hidden" name="<?=$name?>" value="<?=$value?>" id="control_file_<?=$name?>_<?=$id?>" >

	</tr>
</table>
<!-- END form -->