<?php

namespace Extasy\tests\Users\Social\Api\Facebook;

use \Extasy\Users\Social\Api\Facebook\Registration;

class RegistrationTest extends BaseTest {
	const SomeCorrectEmail = 'test@test.d3';
	const NewLogin = 'new_login';
	const NewUID = 'aaaaaaa';
	protected $correctFacebookResponse = [
		'id'   => self::NewUID,
		'name' => 'Extasy Robot'
	];
	protected $correctForm = [
		'login' => self::NewLogin,
		'email' => self::SomeCorrectEmail,
		'captcha' => '',
	];
	/**
	 * @var \Extasy\tests\Mocks\Mailer
	 */
	protected $mailer = null;

	public function setup() {
		parent::setup();
		$this->mailer = new \Extasy\tests\Mocks\Mailer();
		\Email_Controller::setMailer( $this->mailer );
		$this->api->setResult( $this->correctFacebookResponse );
	}

	public function tearDown() {
		parent::tearDown();
		\Email_Controller::setMailer( null );
	}

	/**
	 * @expectedException \NotFoundException
	 */
	public function testWithoutAuth() {
		$this->api->SetException( new \NotFoundException() );
		$api = new Registration( $this->correctForm );
		$api->exec();
	}

	/**
	 * @expectedException \Extasy\Users\Columns\SocialNetworksException
	 */
	public function testUIDAlreadyUsed() {

		$this->api->setResult( [ 'id' => self::UID, 'name' => 'blabla' ] );
		//
		$api = new Registration( $this->correctForm );

		$api->exec();

	}

	/**
	 *
	 */
	public function testLoginAlreadyUsed() {
		//
		$api = new Registration( [
									 'login' => self::Login,
									 'email' => self::SomeCorrectEmail,
									 'captcha' => '',
								 ] );
		try {
			$api->Exec();
			$this->fail();
		}
		catch ( \Exception $e ) {

		}

	}


	public function testEmailAlreadyUsed() {
		$api = new Registration( [
									 'login' => 'new_login',
									 'email' => self::Email,
									 'captcha' => '',
								 ] );
		try {
			$api->exec();
			$this->fail( 'Must fail' );
		}
		catch ( \Exception $e ) {

		}

	}

	public function testRegistration() {
		$schema = \CConfig::getSchema(\UsersRegistration::RegistrationConfirmationConfigName);

		$api = new Registration( $this->correctForm );
		$api->exec();

		$this->assertTrue( $this->mailer->isSent() );
		$lastEmail = \EmailLogModel::getLast();
		$this->assertEquals( $lastEmail->to->getValue(), self::SomeCorrectEmail );
		$user = \UserAccount::getByLogin(self::NewLogin );
		$this->assertEquals( $user->social_networks->getValue()['facebook'], self::NewUID);
	}
} 