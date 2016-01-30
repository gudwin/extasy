<?php

namespace Extasy\tests\Users\Social\Api\Facebook;

use \Extasy\Users\Social\Api\Facebook\Login;
use Faid\DBSimple;


class LoginTest extends BaseTest {
	public function setup() {
		parent::setup();
		$this->api->setResult( [ 'id' => self::UID, 'name' => 'Extasy robot' ] );
	}

	/**
	 * @expectedException \NotFoundException
	 */
	public function testUserNotLogined() {
		$this->api->setException( new \NotFoundException() );
		$api = new Login();
		$api->Exec();
	}

	/**
	 * @expectedException \Extasy\Users\login\UserNotConfirmedException
	 */
	public function testUserNotConfirmed() {
		DBSimple::update( \UserAccount::getTableName(),
						  [ 'confirmation_code' => 'some_value' ],
						  [ 'id' => 1 ] );
		$api = new Login();
		$api->exec();
	}

	/**
	 * @expectedException \Extasy\Users\Columns\SocialNetworksException
	 */
	public function testUnknownUID() {
		$this->api->setResult(['id' => 'unknown_uid','name' => 'Unknown user name']);
		$api = new Login();
		$api->exec();
	}


	public function testLogin() {
		$api = new Login();
		$api->exec();
		$this->assertTrue( \UsersLogin::isLogined() );
		$this->assertEquals( \UsersLogin::getCurrentSession()->login->getValue(), self::Login);

	}
} 