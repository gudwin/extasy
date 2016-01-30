<?php
namespace Extasy\tests\Api;

use Extasy\Api\ApiOperation;
use Extasy\Api\ApiController;

class TestApiOperationWithParams extends ApiOperation {
	const MethodName = 'test.HelloWithParams';
	protected $requiredParams = array('msg');
	protected $optionalParams = array( 'msg2');
	public function action( ) {
		$result = array(
			'msg' => $this->getParam('msg')
		);
		$msg2 = $this->getParam( 'msg2', null);
		if ( !empty( $msg2 )) {
			$result['msg2'] = $msg2;
		}
		return $result;
	}
	public static function init( ) {

		$controller = ApiController::getInstance();
		$controller->add( new TestApiOperationWithParams());
	}
}