<?php
/**
 * Класс для отображения попапов
 * @author Gisma
 *
 */
class CMSDesignPopup {
	public function begin($title = 'CMS - PopUp',$szAddToHead = '') {
		$szHTTP_ROOT = \Extasy\CMS::getWWWRoot();
		?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?=$title?></title>
	<?php
	CMSDesign::getInstance()->layout->initialScriptsAndCSS(); 
	?>

	<?=$szAddToHead?>
</head>
<body class="PopupBody">
		<table style="width:100%">
		<tr>
			<td>
<?
	}
	public function header($value = '') {
		?>
			<h2 class="PopupHeader"><?=$value?></h2>
		<?
	}
	public function end() {
		?>
			</td>
		</tr>
		</table>
</body>
</html>
<?
	}
	
}