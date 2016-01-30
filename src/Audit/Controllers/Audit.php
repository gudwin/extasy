<?
namespace Extasy\Audit\Controllers;

use Faid\View\View;
use Extasy\Audit\Api\ApiOperation;
class Audit extends \adminPage {
	public function __construct() {
		$this->setupAclActionsRequiredForAction( array(\CMSAuth::AuditorRoleName ));
		parent::__construct();
	}
	public function main( ) {
		$title = ('Средства аудита');
		$begin = array(
			$title => '#'
		);
		$view = new View( __DIR__ . '/../Views/index.tpl' );

		$this->outputHeader(  $begin, $title );
		print $view->render();
		$this->outputFooter();
		$this->output();
	}
	public static function startUp( ) {
		$page = new Audit();
		$page->process();
	}
}