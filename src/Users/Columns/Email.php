<?php


namespace Extasy\Users\Columns;

use \Faid\View\View;
class Email extends \Extasy\Columns\Input {
	public function getAdminFormValue() {
		$view = new View( __DIR__ . '/email.tpl');
		$view->set( 'name', $this->szFieldName );
		$view->set( 'value', $this->aValue );

		return $view->render();
	}
} 