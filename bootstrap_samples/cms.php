<?php
// Extasy 5 Loader
use \Faid\Dispatcher\Dispatcher;
use \Extasy\Request;
use \Extasy\CMS;
use \Faid\Configure\Configure;
//
loadKernel();

set_time_limit( 60 );
//-------------------------------------------------------------
// This is example of initialization without environments
// delete this section in real projects
include __DIR__ . '/test-environment.php';

$request = new Request();
$dispatcher = new Dispatcher( $request );

$cms = new CMS( $dispatcher, 'tests' );
$cms->dispatch();

// END OF SECTION
//-------------------------------------------------------------

//
//$request = new Request();
//
//$dispatcher = new Dispatcher( $request );

//$cms = new CMS( $dispatcher );
//$cms->dispatch();


function loadKernel( ) {
	
	$kernelPath = detectKernelPath();
	
	require_once $kernelPath ;

}
function detectKernelPath() {
	$possiblePaths = array(
		__DIR__  . DIRECTORY_SEPARATOR . '../vendor/gudwin/Extasy/src/bootstrap.php', // in case of website run
		__DIR__ .  DIRECTORY_SEPARATOR . '../src/bootstrap.php' // in case extasy runs in development mode
	);
	foreach ( $possiblePaths as $path) {
		if ( file_exists( $path ) ) {
			return $path;
		}
	}
	throw new Exception('Extasy CMS loader not found. Try check installation');
}