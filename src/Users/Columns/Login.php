<?php


namespace Extasy\Users\Columns;


use Faid\DBSimple;

class Login extends \Extasy\Columns\Input {
	public function setValue( $newValue ){
		if ( !$this->isUniq( $newValue)) {
			throw new \ForbiddenException('Login `'.$newValue .'` already used');
		}
		if ( empty( $newValue )) {
			throw new \InvalidArgumentException('Login can`t be empty');
		}
		parent::setValue( $newValue );
	}
	protected function isUniq( $login) {
		$count = DBSimple::getRowsCount(USERS_TABLE, array(
			'login' => $login,
			sprintf('id <> %d', $this->document->id->getValue()),
		));
		return $count == 0;
	}
}