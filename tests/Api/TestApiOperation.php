<?php
namespace Extasy\tests\Api;

use Extasy\Api\ApiOperation;
use Extasy\Api\ApiController;

class TestApiOperation extends ApiOperation {

	const  MethodName = 'Test.Hello';
	const result = 'Hello world!';


	public function action() {
		return self::result;
	}
	public static function init( ) {
		$controller = ApiController::getInstance();
		$controller->add( new TestApiOperation());
	}
}