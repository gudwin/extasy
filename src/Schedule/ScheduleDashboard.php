<?php

namespace Extasy\Schedule;

use Extasy\CMS;

class ScheduleDashboard extends \adminPage {
	const Title = 'Управление планировщиком';

	protected $aclActionList = [ \CMSAuth::SystemAdministratorRoleName ];


	public function main() {
		$fieldInfo = \Extasy\Schedule\Job::getFieldsInfo();
		$begin   = [ self::Title => '#' ];

		$this->outputHeader( $begin, self::Title  );

		$view = new \Faid\View\View( __DIR__ . '/dashboard.tpl');
		$view->set('statuses', ( $fieldInfo['fields']['status']['values']));
		print $view->render();
		$this->outputFooter();
		$this->output();
	}
} 