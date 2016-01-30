<?
use \Faid\DB;
use \Faid\UParser;

//************************************************************//
//                                                            //
//           Класс управления забытым паролем                 //
//       Copyright (c) 2008 Ext-CMS (http://www.ext-cms.com/) //
//               отдел/сектор                                 //
//       Email:    info@gisma.ru (http://www.gisma.ru/)       //
//                                                            //
//  Разработчик: Gisma (26.01.2009)                           //
//  Модифицирован:  26.01.2009  by Gisma                      //
//                                                            //
//************************************************************//

class UsersForgot {
	const LogName = 'Users.Forgot';
	const EmailConfigName = 'users.forgotEmail';

	public static function send( $email ) {
		CMSLog::addMessage( self::LogName, sprintf( 'Email - "%s"', $email ) );
		self::validateLoginAttempts( $email );
		//
		$aAccount = self::lookForEmail( $email );
		//
		$schema = CConfig::getSchema(self::EmailConfigName);
		$values = $schema->getValues();
		//
		$szContent = UParser::parsePHPCode( $values['request_content'], $aAccount );
		//
		Email_Controller::send(
			$aAccount[ 'email' ],
			$values['request_subject'],
			$szContent
		);
		//
	}

	protected static function lookForEmail( $email ) {
		$sql    = 'SELECT * FROM `%s` WHERE STRCMP(`email`,"%s") = 0';
		$sql    = sprintf( $sql,
						   USERS_TABLE,
						   $email );
		$aFound = DB::get( $sql );
		if ( empty( $aFound ) ) {
			throw new \NotFoundException( 'Email not found in database' );
		}
		return $aFound;
	}
	public static function resetPassword( $user ) {
		self::validateLoginAttempts( $user->email->getValue() );
		$password = substr( md5(time()),0,8);
		$user->password = $password;
		$user->update();
		//
		$parseData = $user->getData();
		$parseData['password'] = $password;
		//
		$schema = CConfig::getSchema(self::EmailConfigName);
		$values = $schema->getValues();
		//
		Email_Controller::parseAndSend(
			$user->email->getValue(),
			$values['newpassword_subject'],
			$values['newpassword_content'],
			$parseData
		);
	}
	protected static function validateLoginAttempts( $email ) {
		try {
			UsersLogin::testLoginAttempts();
		}
		catch ( \Extasy\Users\login\LoginAttemptsException $e ) {
			try {
				$user = self::lookForEmail( $email );
				$e->blockUser( new UserAccount( $user ) );
				throw $e;
			}
			catch ( \NotFoundException $e ) {
			}
		}
	}
}

?>