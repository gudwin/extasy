<?
namespace Extasy\Dashboard\Controllers;
use \adminPage;
use \EventController;
use \CMSAuth;
use \CMSDesign;
class Index extends adminPage {
	const EventName = 'Dashboard.Index';
	public function main() {
		$szTitle = 'Вас приветствует Система Управления "'.SITE_NAME.'"';
		$aBegin = array(
			'Приветствие' => '#'
		);

		$this->outputHeader($aBegin,$szTitle);

		$design = CMSDesign::getInstance();
		EventController::callEvent( self::EventName,$design);
		$this->outputFooter();
		$this->output();
	}
	public function showLoginForm( ) {
		CMSAuth::getInstance()->check();
		$this->main();
	}
}
?>