<?php


namespace Extasy\tests\Users;

use \Extasy\tests\Helper;
use \UsersLogin;
use \Extasy\tests\system_register\Restorator;
use \Extasy\Users\registration\Dashboard\Email as RegistrationAcceptedEmail;
use \Extasy\Users\registration\Dashboard\EmailConfirmation as RegistrationConfirmationEmail;


abstract class UsersTest extends \Extasy\tests\BaseTest {
	const Login        = 'root';
	const UnknownLogin = 'unknown';
	const Email        = 'test@test.com';
	const Password     = 'a#12345678';
	const UnknownPassword = 'unknown#1';

	public function setUp() {
		parent::setUp();
		Restorator::restore();


		Helper::setupUsers( array( array(
									   'login'    => self::Login,
									   'password' => self::Password,
									   'email'    => self::Email
								   )
							) );

		$this->configureLoginAttempts(5,10);
		$this->cleanUpSchemes();

		RegistrationAcceptedEmail::install();
		RegistrationConfirmationEmail::install();
	}
	protected function cleanUpSchemes() {
		try {
			RegistrationAcceptedEmail::uninstall();
		} catch (\CConfigException $e ) {

		}
		try {
			RegistrationConfirmationEmail::uninstall();
		} catch (\CConfigException $e ) {

		}
	}
	public function tearDown()
	{
		parent::tearDown();
		$this->cleanUpSchemes();
	}

	protected function configureLoginAttempts( $perSession, $perHosts ) {
		$register                    = new \SystemRegister( UsersLogin::SystemRegisterPath );
		$register->PerSession->value = \IntegerHelper::toNatural( $perSession );
		$register->PerHost->value    = \IntegerHelper::toNatural( $perHosts );
		\SystemRegisterSample::createCache();
	}
} 