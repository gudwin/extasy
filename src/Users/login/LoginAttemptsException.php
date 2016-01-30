<?php

namespace Extasy\Users\login;


class LoginAttemptsException extends \Exception {
	const LogName = 'Users.Blocked';

	public function blockUser( \UserAccount $user ) {
		\CMSLog::addMessage( self::LogName, sprintf( 'Users with login %s blocked ', $user->login->getValue() ) );
		$user->confirmation_code = \UsersRegistration::getConfirmationCode();
		$user->update();
	}
}