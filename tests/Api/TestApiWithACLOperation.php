<?php
namespace Extasy\tests\Api;

use Extasy\Api\ApiOperation;
use Extasy\Api\ApiController;

class TestApiWithACLOperation extends ApiOperation {
	const MethodName = 'test.HelloWithACL';
	const RightName = 'testRight';
	protected $requiredACLRights = array(
		'testRight'
	);

	public function action() {
		return true;
	}
}