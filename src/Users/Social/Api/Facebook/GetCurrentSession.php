<?php
namespace Extasy\Users\Social\Api\Facebook {
	class GetCurrentSession extends \Extasy\Api\ApiOperation {
		const MethodName = 'users.facebook.getCurrentSession';
		protected function action() {
			$facebookApi = \Extasy\Users\Social\FacebookApiFactory::getInstance();
			return $facebookApi->getCurrentSession();
		}
	}
}