<?php
/**
 * Class extasyTestAdminPage
 * @package extasycms.testSuite
 */
class extasyTestAdminPage {
	public static function main() {
		$page = new extasytestAdminLauncher( );
		$page->process( );
	}
	public static function edit() {
		$page = new CMS_DataManage();
		$page->process();
	}
	public static function quick_add( ) {
		$page = new extasyTestQuickAddAdminPage();
		$page->process();
	}
	public static function dataList( ) {
		$_GET['type'] = $_POST['type'] = extasyTestModel::ModelName;
		$page = new CMS_Page_DataList();
		$page->viewBeginPath = array(
			'Тестирование сайта' => 'index.php',
		);
		$page->addButtons(array('Назад к тестированию' => './index.php'));
		$page->addButtons(array('Быстрое добавление' => './quick_add'));
		$page->process();
	}
}