<?php

namespace Extasy\Dashboard\Controllers;


class Security extends \adminPage {
	const Title = 'Общая безопасность';
	protected $aclActionList = [\CMSAuth::SystemAdministratorRoleName ];

	public function main() {

		$this->outputHeader( [self::Title => '#' ], self::Title );


		$design = \CMSDesign::getInstance();
		$design->FormBegin();

		$checkbox = new \CCheckbox();
		$checkbox->name = 'enable_https';
		$checkbox->value = 1;
		$checkbox->title = 'Включить поддержку HTTPS';
		$design->tableBegin();
		$design->fullRow( $checkbox );
		$design->tableEnd();
		$design->submit('submit','Сохранить');

		$design->formEnd();
		$this->outputFooter();
		$this->output();
	}
} 