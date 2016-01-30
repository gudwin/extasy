<?php
namespace Extasy\Users\login\controller;

use \Exception;
use \UsersLogin;
use \Faid\Configure\Configure;
class Logout extends AuthorizationRequired {
	const ScriptPath = 'Users/scripts/logout.php';
	const Title = 'Выйти из системы';
	const UrlKey = 'logout';
	
	const LogoutUrlKey = 'Users.logoutUrl';
	public function main() {
		try {
			UsersLogin::logout();
		}
		catch ( Exception $e ) {
		}
		$this->jump( $this->getLogoutUrl() );
	}
	protected function getLogoutUrl() {
		return Configure::read( self::LogoutUrlKey );
	}
}