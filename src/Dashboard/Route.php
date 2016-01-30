<?php
namespace Extasy\Dashboard {
	use \CMSAuth;
	use \Faid\Dispatcher\RouteException;

	class Route extends \Faid\Dispatcher\HttpRoute {
		
		public function prepareRequest() {
			parent::prepareRequest();
            if ( !defined('CMS')) {
                define('CMS',1);
            }

			// Activate CMS auth
			\CMS_Strings::getInstance()->setActiveLang('RUSSIAN');
			\EventController::callEvent('dashboardInit');
		}
        public function dispatch() {
            $granted = \CMSAuth::getInstance()->check();
            if ( $granted ) {
                parent::dispatch();
            } else {
            	
            }
        }
	}
}