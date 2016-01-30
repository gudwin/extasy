<?
use \Faid\Dispatcher\Dispatcher;
use \Extasy\Request;
use \Extasy\CMS;
use \Faid\Configure\Configure;

$_SERVER['TESTS'] = true;
// Установка времени (необходимо с PHP 5.3)
date_default_timezone_set('Europe/Madrid');

require_once dirname( __FILE__ ) . '/../src/bootstrap.php';

Configure::write('Debug', true);



// PHPUnit tries to backup global variables during testing
// But PHP not allows to serialize closure functions, exception raises
// So any global variable with closure inside will crash PHPUnit
$dispatcher = null;



include __DIR__ . '/../bootstrap_samples/test-environment.php';

$request    = new Request();
$dispatcher = new Dispatcher( $request );
new CMS( $dispatcher, 'tests' );
// phpunit tries to serialize global variables
// $dispatcher could contain closures, so this global variable will break PHPUnit
$dispatcher = null;
?>