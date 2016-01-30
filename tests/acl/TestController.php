<?php


namespace Extasy\tests\acl;
use \SiteMapController;
class TestController extends SiteMapController {
	protected $mainCalled = false;
	public function main( ) {
		$this->mainCalled = true;
	}
	public function isMainCalled( ) {
		return $this->mainCalled;
	}
	public function setRequiredRights( $rightsList ) {
		$this->setupAclActionsRequiredForAction( $rightsList);
	}

} 