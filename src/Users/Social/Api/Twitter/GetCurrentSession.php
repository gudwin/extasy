<?php
namespace Extasy\Users\Social\Api\Twitter {
	class GetCurrentSession extends \Extasy\Api\ApiOperation {
		const MethodName = 'users.twitter.getCurrentSession';
		protected function action() {
			$twitterApi = \Extasy\Users\Social\TwitterApiFactory::getInstance();
			return $twitterApi->getCurrentSession();
		}
	}
}