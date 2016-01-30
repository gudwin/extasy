<?php

namespace Extasy\Users\Columns;

use \Faid\View\View;

class ConfirmationCode extends \Extasy\Columns\Input {
	public function getAdminFormValue() {
		$view = new View( __DIR__ . '/confirmation_code.tpl' );
		$view->set( 'name', $this->szFieldName );
		$view->set( 'value', $this->aValue );
		return $view->render();
	}
} 