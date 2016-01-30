<?php


namespace Extasy\Users\Api;


class Login extends \Extasy\Api\ApiOperation {
	protected $requiredParams = array(
		'login',
		'password'
	);

	protected function action() {
		\UsersLogin::login( $this->getParam( 'login' ), $this->getParam( 'password' ) );
		return true;
	}
} 