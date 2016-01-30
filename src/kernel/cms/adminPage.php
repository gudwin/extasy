<?php
/**
 *
 * Basic class for admin pages
 * @author Gisma
 *
 */
class adminPage extends extasyPage {
	const debugCode = 2;

	protected $embed = false;

	public function main() {}
	public function showAlerts() {
		CMSDesign::getInstance()->messages->showAlerts();
	}

	/**
	 * Отображает header стандартной страницы
	 */
	public function outputHeader($aBegin, $szTitle, $aScript = array(), $aCSS = array(), $embed = false) {
		$this->embed = $embed;
		if ( !is_array($aCSS) ) {
			$aCSS = array($aCSS);
		}
		if ( !is_array($aScript) ) {
			$aScript = array($aScript);
		}

		$szAddToHead = '';
		// Перебор скриптов 
		$aAdd = array();
		foreach ($aScript as $row) {
			$aAdd[ ] = sprintf('<script type="text/javascript" src="%s"></script>', $row);
		}
		foreach ($aCSS as $row) {
			$aAdd[ ] = sprintf('	<link rel="stylesheet" type="text/css" href="%s" />', $row);
		}
		$szAddToHead = implode("\r\n", $aAdd);


		$design = CMSDesign::getInstance();
		if ( !$embed ) {
			$design->layout->Begin($aBegin, $szTitle, '', $szAddToHead);
			$design->layout->documentBegin();
		} else {
			$design->popup->begin($szTitle, $szAddToHead);
		}

	}

	/**
	 * Отобржает футер стандартной страницы
	 */
	public function outputFooter() {
		$design = CMSDesign::getInstance();
		if ( !$this->embed ) {
			$design->layout->documentEnd();
			$design->layout->End();
		} else {
			$design->popup->end();
		}
	}
	protected function testIfDashboardMenuShouldBeShown() {
		return true;
	}
	public function initilizeDashboardMenu( ) {
		parent::initilizeDashboardMenu();
		$this->menu->setDashboardFlag();

	}

	public static function addAlert($szMessage) {
		if ( empty($_SESSION[ 'cms_message' ]) ) {
			$_SESSION[ 'cms_message' ] = array();
		}
		$_SESSION[ 'cms_message' ][ ] = ($szMessage);
	}

	protected function insertIntoResponse( $insert ) {
		$contents    = ob_get_contents();
		$postfix     = '</body>';
		$newContents = str_replace($postfix, $insert . $postfix, $contents );
		if ( strlen ( $newContents) != strlen( $contents )) {
			ob_clean();
			print $newContents;
		}
	}

	/**
	 * Отображает заголовок страницы на которой будет Ext JS панель
	 */
	protected function outputExtJSHeader($aBegin, $szTitle, $add_script = array(), $add_css = array()) {

		$this->outputHeader($aBegin, $szTitle, $add_script, $add_css);

	}
}