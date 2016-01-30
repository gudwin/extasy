<?php
use \Faid\Dispatcher\Dispatcher;
use \Extasy\Request;
use \Extasy\CMS;

$commonDir = __DIR__ . DIRECTORY_SEPARATOR;

require_once $commonDir . '../../../../../../../Vendors/Extasy/src/bootstrap.php';

$request = new Request();

$dispatcher = new Dispatcher( $request );
//
new CMS( $dispatcher );

require_once CLASS_PATH.'loader/loader.class.php';

include $commonDir .'common.php';
