<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<HEAD>
<TITLE> Редактирование изображения [ <?=$path;?> ]</TITLE>
<META NAME="Generator" CONTENT="Extasy">
<META NAME="Author" CONTENT="">
<META NAME="Keywords" CONTENT="">
<META NAME="Description" CONTENT="">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<style type="text/css">

html, body {height:100%}
*, body {padding:0; margin:0;}
body {font:12px Tahoma, sans-serif; color:#717171; background-color:#F7F7FF;}
.hiddenFrame {
	visibility:hidden;
	position:absolute;
	left: -1000px;
	top: -1000px;
}
</style>
<script>
var source = '';
/**
*   @desc Вставляет изображение
*/
function insertImage() {

	var thumb = document.getElementById('insertImageThumb');
	var style = document.getElementById('insertImageStyle');
	var border = document.getElementById('insertImageBorder');
	var width = document.getElementById('insertImageWidth');
	var height = document.getElementById('insertImageHeight');
	var thumbWidth = document.getElementById('insertImageThumbWidth');
	var thumbHeight = document.getElementById('insertImageThumbHeight');
	var alt = document.getElementById('insertImageAlt');
	var align = document.getElementById('insertImageAlign');
	var html = '';

	if ((thumb != null) && (thumb.checked))
	{
		// значит вставляет путь до тумбы
		html += '<a href="#" onclick="window.open(\'<?=\Extasy\CMS::getDashboardWWWRoot()?>zoom.php?path=<?=$path?>\',\'_blank\',\'';
		html += "height=" + height.value + ", width =" + width.value + ",location = no,menubar = no,resizable = no,scrollbars = yes,status = no,titlebar = no";
		html += '\')">';
		html += '<img src="<?=\Extasy\CMS::getDashboardWWWRoot()?>zoom.php?path=<?=$szPathToThumb?>" ';
		html += ' border=' + border.value + 'px ';
		html += ' width=' + thumbWidth.value + 'px';
		html += ' height=' + thumbHeight.value + 'px';
		html += ' alt="' + alt.value + '"';
		html += ' style="float:' +  align.value + ';' + style.value + ';">';
		html += '</a>'
	} else {
		html = '<img src="<?=\Extasy\CMS::getDashboardWWWRoot()?>zoom.php?path=<?=$path?>" ';
		html += ' border=' + border.value + 'px ';
		html += ' width=' + width.value + 'px';
		html += ' height=' + height.value + 'px';
		html += ' alt="' + alt.value + '"';
		html += ' style="float:' +  align.value + ';' + style.value + ';">';
	
	}

	if (window.parent.opener)
	{
		window.parent.opener.insertHTML(html);
		window.parent.close();
	}
	
}
/**
*   @desc Устанавливает текущий блок
*/
function setCurrentBlock(szBlockName) {
	// получаю центральную часть
	var obj = document.getElementById('central');
	
	// очищаем внутренности
	obj.innerHTML = '';
	// подсовываем
	source = document.getElementById(szBlockName);
	
	source.className = '';
	obj.appendChild(source);
}
/**
*   @desc Восстанавливает текущий блок
*/
function restoreBlock() {
	source.className = 'hiddenFrame';
	// получаю центральную часть
	var obj = document.getElementById('central');
	// удаляем элемент
	obj.removeChild(source);
	document.body.appendChild(source);
}
/**
*   @desc Выводит диалог генерации тумбы
*/
function outputGenerateThumbnail() {
	setCurrentBlock('generateThumb');
}
/**
*   @desc Выводит диалог вставки изображения
*/
function outputInsertImage() {
	window.parent.opener.saveBrowserCallBack('<?=\Extasy\CMS::getDashboardWWWRoot()?>zoom.php?path=<?=$path?>');
	window.parent.close();
	//setCurrentBlock('insertImage');
}
/**
*   @desc Выводит диалог операции
*/
function changeOperation(objSelect) {
	// восстанавливаем блоки
	restoreBlock();
	// определяю кого выводить 
	switch (objSelect.selectedIndex)
	{
	case 0:break;
	case 1:
		
		outputGenerateThumbnail();
		break;
	case 2:
		outputInsertImage();
		break;
	default:break;
	}
	// восстанавливаем состояние селета
	objSelect.selectedIndex = 0;
	// вызываю нужную функцию
	// 
}
/**
*   @desc Вызывается при старте изображение
*/	
function init() {
	source = document.getElementById('centralText');
}
</script>
</HEAD>

<BODY onload="init();">
	<table border=0 width="600px" cellpadding="0" cellspacing="0"> 
		<tr>
			<td rowspan="2" width="200px" valign="top">
				<img src="./showimage.php?path=<?=$path?>&preview=1&x=<?=rand(0,100000)?>">
				<?
				if (!empty($bThumb)) {
					printf('<h3> Картинка </h3><Br>');
					printf("<img src='showimage.php?path=%s&x=<?=rand(0,100000)?>'>",$szPathToThumb);
				} else {
				}
				?>
			</td>
			<td valign="top" width="400px" > <nobr>
				<span align="right" style="margin-left:50px;">
				Операции :
				<select width="100px" onchange="changeOperation(this);">
					<option> Выберите операцию </option>
					<option> Генирировать иконку</option>
					<script>
						if ((window.parent.opener)&& (window.parent.opener.fileBrowserCallBack))
						{
							document.writeln("<option> Вставить изображение в редактор</option>");
							
						}
					</script>
				</select>
				</span>
				</nobr>
				<!-- Основная часть -->
				<div id="central">
					<div id="centralText">
					<h3> Вы находитесь в диалоге редактирования изображений</h1> 
					<h5> Чтобы перейти к выполнению операций, откройте список в правом верхнем углу страницы</h5>
					<h5>В левой верхней колонке вы видите, уменьшенный вариант изображения</h5>
					<h5>В левой нижней колонке вы видите, сущ. иконку на изображение (используется при вставке в редакторе)</h5>
					</div>
				</div>
			</td>
		</tr>
		<tr>
			<td rowspan="2" valign="top">

			</td>
		</tr>
	</table>
<div class="hiddenFrame" id="generateThumb">
	<h3> Генерировать иконку </h3>
	<table> 
		<form method="post" action="image.php" enctype="multipart/form-data">
		<tr>
			<td> 
				Ширина
			</td>
			<td> 
				<input type="text" name="width" value="<?=$aThumbInfo['width']?>">
			</td>
		</tr>
		<tr>
			<td> 
				Высота
			</td>
			<td> 
				<input type="text" name="height" value="<?=$aThumbInfo['height']?>">
			</td>
		</tr>
		<tr>
			<td>
				Заменить закаченным изображением 
			</td>
			<td>
				<input type="file" name="file">
			</td>
		</tr>
		<tr>
			<td colspan="2"> 
				<input type="hidden" name="path" value="<?=$path?>">
				<input type="submit" name="submit" value="Генерировать">
			</td>
		</tr>
		</form>
	</table>
</div>
<div class="hiddenFrame" id="insertImage">
	<h3> Вставить изображение в редактор </h3>
	<table cellpadding ="0" cellspacing="0"> 
		<?
			if (!empty($bThumb)) {
		?>
				<tr>
					<td> 
						Вставить иконку 
					</td>
					<td> 
						<input type="checkbox" id="insertImageThumb" name="insertImageThumb" value="1">
						<label for="insertImageThumb">
							Вместо изображения в документ будет вставлена иконка изображения, которое будет являться ссылкой на изображение.
						</label><br>
						Ширина иконки - <input type="text" name="width" id="insertImageThumbWidth" value="<?=$aThumbInfo['width']?>"><br>
						Высота иконки - <input type="text" name="width" id="insertImageThumbHeight" value="<?=$aThumbInfo['height']?>"><br>
					</td>
				</tr>
		<?
			}
		?>
		<tr>
			<td> 
				Ширина видимого изображения
			</td>
			<td> 
				<input type="text" name="width" id="insertImageWidth" value="<?=$aInfo[0]?>">
			</td>
		</tr>
		<tr>
			<td> 
				Высота видимого изображения
			</td>
			<td> 
				<input type="text" name="height" id="insertImageHeight" value="<?=$aInfo[1]?>">
			</td>
		</tr>
		<tr>
			<td> 
				Ширина границы (в пикселях)
			</td>
			<td> 
				<input type="text" name="border" id="insertImageBorder" value="0">
			</td>
		</tr>
		<tr>
			<td> 
				Подпись
			</td>
			<td> 
				<input type="text" name="alt" id="insertImageAlt" value="">
			</td>
		</tr>
		<tr>
			<td> 
				Обтекание 
			</td>
			<td> 
				<select name="align" id="insertImageAlign" >
					<option value="none" selected> Без обтекания </option>
					<option value="right"> По правому краю </option>
					<option value="left"> По левому краю </option>
				</select>
			</td>
		</tr>
		<tr>
			<td> 
				Дополнительные стили
			</td>
			<td> 
				<input type="text" name="style" id="insertImageStyle" value="">
			</td>
		</tr>
		<tr>
			<td colspan="2"> 
				<input type="submit" name="submit" value="Вставить" onclick="insertImage();">
			</td>
		</tr>
	</table>
</div>
</BODY>
</HTML>
