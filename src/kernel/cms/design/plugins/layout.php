<?php
use \Extasy\CMS;
/**
 * Класс для отображения основного поля вывода. Порядок вызова методов:
 * $layout = new CMSDesignLayout();
 * $layout->begin(...);
 * $layout->leftMenu();
 * $layout->documentBegin();
 *    ... // Вот тут выводится контент страница
 * $layout->documentEnd();
 * $layout->end();
 * @author Gisma
 *
 */
class CMSDesignLayout {
	const SessionKey = 'LastActiveCMSMenuIitem';
	/**
	 * Used for active menu item detection
	 * @var string
	 */
	protected $requestedUrl = '';
	/**
	 * @desc Выводит путь к текущему документу и верхушку страницы
	 * @return
	 */
function Begin($aPath = array(), $szSiteName = '', $onLoad = '', $addToHead = '', $szHead = '') {

	$strings = CMS_Strings::getInstance();
	if ( empty($szSiteName) ) {
		$szSiteName = SITE_NAME;
	}
	$szPath = '';
	if ( sizeof($aPath) > 0 ) {

		foreach ($aPath as $key => $value) {
			$aResult[ ] = '<a href="' . $value . '">' . $key . '</a>';
		}
		end($aResult);
		// Последнюю позицию заменяем на просто строку, вместо ссылки
		$aResult[ sizeof($aResult) - 1 ] = '<b><i>' . $key . '</i></b> <img alt="" src="' . CMS::getResourcesUrl() . 'extasy/pic/icons/crumbs_cur.gif" />';
		$szPath                          = implode('<img alt="" src="' . CMS::getResourcesUrl() . 'extasy/pic/icons/crumbs_normal.gif" />', $aResult);

		// Добавляем иконку индексной страницы
		$szPath = '<a href="' . \Extasy\CMS::getDashboardWWWRoot() . '" id="admin_home_link"><img alt="" src="' . CMS::getResourcesUrl() . 'extasy/pic/layout/home.png"/></a> <img alt="" src="' . CMS::getResourcesUrl() . 'extasy/pic/icons/crumbs_normal.gif"/> ' . $szPath;
	}

	?>
	<!DOCTYPE html>
	<html>
	<head>
		<meta charset="utf-8"/>
		<title><?=SITE_NAME?> - <?=$szSiteName?></title>
		<?php
		$this->initialScriptsAndCSS();
		?>
		<?=$addToHead?>
		<script type="text/javascript">
			jQuery(function ($) {
				$('#cms_script_select').change(function () {
					if ($(this).val().length > 0) {
						window.location = $(this).val();
					}
				});
			});
		</script>
	</head>
	<body style="background: #4a97ce url('<?= CMS::getResourcesUrl() ?>extasy/pic/layout/bg.gif') 0% 0% repeat-x;" id="extasy_body" onload="<?= $onLoad ?>">
	<table style="width:100%">
	<tr>
		<td class="LayoutTop">
			<table style="width:100%">
				<tr>
					<td class="SiteLogo"><p><!--  --></p>
					</td>
					<td style="width:20%"><!-- --></td>
					<?=$szHead?>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
	<td>
	<div class="Crumbs"><?=$szPath?></div>
	<br/>
	<table class="LayoutCenter" style="width:100%">
	<tr>
<?
}

function documentBegin() {
	?>
<td class="LayoutCenterR" valign="top" rowspan="1">    <?
}
function documentEnd() {
	?></td><?php
}
function End() {
	?>
	</tr>

	</table>
	<div class="Footer">
	</div>
	</td>
	</tr>
	</table>
	</body>

	</html>
<?
}
	/**
	 * Данный метод все JS & CSS скрипты, которые всегда грузятся при загрузке движка
	 */
public function initialScriptsAndCSS() {

	CMSDesign::insertCSS( CMS::getResourcesUrl() . 'extasy/ext3/resources/css/ext-all.css');
	CMSDesign::insertCSS( CMS::getResourcesUrl() . 'extasy/css/default-theme.css');
	CMSDesign::insertScript( CMS::getResourcesUrl() . 'extasy/js/locale/' . strtolower(OUTPUT_LANGUAGE) . '.js');

	?>
	<script type="text/javascript">
		var systemInfo = {
			http_root: <?php print json_encode(\Extasy\CMS::getDashboardWWWRoot())?>
		};
	</script>
	<!-- GLOBAL:PLACE JS HERE, PLEASE--><script type="text/javascript" src="http://static.hockey.local/resources/vendors/jquery.min.js"></script><script type="text/javascript" src="http://static.hockey.local/resources/vendors/angular/angular.js"></script><script type="text/javascript" src="http://static.hockey.local/resources/vendors/angular/angular-animate.js"></script><script type="text/javascript" src="http://static.hockey.local/resources/vendors/angular/angular-sanitize.js"></script><script type="text/javascript" src="http://static.hockey.local/resources/vendors/angular/angular-resource.js"></script><script type="text/javascript" src="http://static.hockey.local/resources/vendors/angular/sortable.js"></script><script type="text/javascript" src="http://static.hockey.local/resources/vendors/angular/date.js"></script><script type="text/javascript" src="http://static.hockey.local/resources/vendors/angular/angular-route.js"></script><script type="text/javascript" src="http://static.hockey.local/resources/vendors/angular/ui-bootstrap-0.13.0.js"></script><script type="text/javascript" src="http://static.hockey.local/resources/vendors/angular/ui-bootstrap-tpls-0.13.0.js"></script><script type="text/javascript" src="http://static.hockey.local/resources/extasy/js/api.js"></script><script type="text/javascript" src="http://static.hockey.local/resources/extasy/js/extasyApi.js"></script><script type="text/javascript" src="http://static.hockey.local/resources/extasy/js/vendors/message.js"></script><!-- THANK YOU FOR JS -->
    <!-- PLACE JS HERE, PLEASE--><script type="text/javascript" src="/resources/extasy/js/vendors/jquery-1.10.2.min.js"></script><script type="text/javascript" src="/resources/extasy/js/vendors/jquery-migrate-1.2.1.js"></script><script type="text/javascript" src="/resources/extasy/js/vendors/bootstrap.js"></script><script type="text/javascript" src="/resources/extasy/js/vendors/bootstrap.submenu.js"></script><script type="text/javascript" src="/resources/extasy/js/vendors/sprintf.min.js"></script><script type="text/javascript" src="/resources/extasy/js/jquery-ui-1.10.3.custom.min.js"></script><script type="text/javascript" src="/resources/extasy/js/vendors/datepicker-ru.js"></script><script type="text/javascript" src="/resources/extasy/js/controller.js"></script><script type="text/javascript" src="/resources/extasy/js/net.js"></script><script type="text/javascript" src="/resources/extasy/js/contentloader.js"></script><script type="text/javascript" src="/resources/extasy/js/sysutils.js"></script><script type="text/javascript" src="/resources/extasy/js/dtree.js"></script><script type="text/javascript" src="/resources/extasy/js/vendors/tmpl.js"></script><script type="text/javascript" src="/resources/extasy/js/vendors/message.js"></script><script type="text/javascript" src="/resources/extasy/js/cms/main.js"></script><script type="text/javascript" src="/resources/extasy/js/cms/hints.js"></script><script type="text/javascript" src="/resources/extasy/js/cms/popup.js"></script><script type="text/javascript" src="/resources/extasy/js/cms/message.js"></script><script type="text/javascript" src="/resources/extasy/js/cms/editDocument.js"></script><script type="text/javascript" src="/resources/extasy/js/administrative/testSuite/index.js"></script><!-- THANK YOU FOR JS -->



	<?php
	CMSDesign::insertScript( CMS::getResourcesUrl() . 'extasy/ext3/adapter/ext/ext-base.js');
	CMSDesign::insertScript( CMS::getResourcesUrl() . 'extasy/ext3/ext-all.js');
	CMSDesign::insertScript( CMS::getResourcesUrl() . 'extasy/ext3/App/App.js');
}

	/**
	 * Отображает меню админки-сайта
	 */
	public function leftMenu() {
	}
}