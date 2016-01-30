<?php
/**
 * @package extasycms.testSuite
 * @author Gisma
 *
 */
class extasytestAdminRouter {
	/**
	 * 
	 */
	public function launch( ) {
		$page = new extasytestAdminLauncher( );
		$page->process( );
	}
	/**
	 * 
	 */
	public function edit() {

		$page = new CMS_DataManage();
		$page->viewBeginPath = array(
				'Тестирование сайта' => 'index.php',
				);
		$page->process();
	}

	/**
	 *
	 */

	/**
	 * 
	 */
	public function dataList( ) {
		$_GET['type'] = $_POST['type'] = extasyTestModel::ModelName;

		$page = new \Extasy\sitemap\controller\DataList();
		$page->viewBeginPath = array(
				'Тестирование сайта' => 'index.php',
		);
		$page->process();
	}
}