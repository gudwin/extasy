<?
use \Faid\Dispatcher\Dispatcher;
use \Extasy\Request;

if (file_exists('../src/bootstrap.php')) {
	require_once '../src/bootstrap.php';
	$request = new Request();

	$dispatcher = new Dispatcher( $request );
//
	new \Extasy\CMS( $dispatcher );
} else {
	die('Can`t detect kernel');
}

class HtmlareaPage extends extasyPage {
	function __construct() {
		parent::__construct();
		$this->addPost('content','store');		
	}
	function store($content) {
		$area = new CHtmlarea();
		$area->postHTMLArea();
	}

	function main() {
		$area = new CHtmlarea();
		$area->showHTMLArea();
		$this->output();
	}
}
$page = new HtmlareaPage(); 
$page->process();

?>