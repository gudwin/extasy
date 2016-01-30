<?php
namespace Extasy\Users\Social\Api\Vkontakte {
	class GetCurrentSession extends \Extasy\Api\ApiOperation {
		const MethodName = 'users.vkontakte.getCurrentSession';
		protected function action() {
			$vkontakteApi = \Extasy\Users\Social\VkontakteApiFactory::getInstance();
			return $vkontakteApi->getCurrentSession();
		}
	}
}