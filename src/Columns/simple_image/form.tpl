<script>
	if (window.$setFileControlValue == null)
	{
		window.$setFileControlValue = function($value) {
			document.getElementById(window.$FileControlId).value = $value;
			document.getElementById(window.$FileControlImageId).src = $value;
			document.getElementById(window.$FileControlId + '_div').innerHTML = 'Путь : ' + $value;
		}
	}
</script>
<table>
	<tr>
		<td>
			Чтобы выбрать изображение, нажмите на кнопку
		</td>
		<td>
			<input type="button" onclick="
			window.$FileControlId = 'simple_file_<?=$name?>';
			window.$FileControlImageId = 'simple_file_<?=$name?>_image';
			window.open('<?=\Extasy\CMS::getDashboardWWWRoot()?>fm/','_blank','scrollbars=yes,width=1024,height=768,left=0,top=0,toolbar=no,titlebar = no, status = no, resizeable = no, menubar = no,location = no, directories = no, fullscreen = yes');return false;" value="Выбрать файл">
		</td>
	</tr>
	<tr>
		<td><input type="button" value="Удалить" onclick="
			document.getElementById('simple_file_<?=$name?>_div').innerHTML = ' Путь : ';
			document.getElementById('simple_file_<?=$name?>_image').src = '';
			return false;
		"></td>
		<td></td>
	</tr>
	<tr>
		<td colspan="2">
			<div id="simple_file_<?=$name?>_div"> Путь : <?=$value?></div>
			<? if (!empty($value)):?>
			<img src="<?=$value?>" title="" id="simple_file_<?=$name?>_image" />
			<?else:?>
			<img src="<?=\Extasy\CMS::getResourcesUrl()?>extasy/img/s.gif" title="" id="simple_file_<?=$name?>_image" />
			<? endif?>

		</td>

	</tr>
</table>
<input type="hidden" name="<?=$name?>" value="<?=$value?>" id="simple_file_<?=$name?>" >