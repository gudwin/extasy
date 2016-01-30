<?php

namespace Extasy\tests\Users;

use \Extasy\tests\Helper;
use Faid\DBSimple;
use \UsersLogin;
use \Extasy\Users\Login\LoginInfo;
use \Extasy\Users\Login\LoginAttempt;

class LoginInfoTest extends UsersTest {
	const FirstIpFixture  = '8.8.8.8';
	const SecondIpFixture = '8.8.8.6';

	public function setUp() {
		parent::setUp();

		Helper::dbFixture( UsersLogin::LoginAttemptsTable,
						   array() );

	}

	public function testLoginAppear() {
		UsersLogin::login( self::Login, self::Password );
		$attempt = new LoginAttempt();
		$this->AssertTrue( $attempt->get( 1 ) );
		$this->assertEquals( $attempt->user_id->getValue(), 1 );
		$this->assertEquals( $attempt->status->getValue(), LoginAttempt::SuccessStatus );
	}

	public function testFailureAppear() {
		try {
			UsersLogin::login( self::Login, self::UnknownPassword );
		}
		catch ( \ForbiddenException $e ) {

		}
		$attempt = new LoginAttempt();
		$this->AssertTrue( $attempt->get( 1 ) );
		$this->assertEquals( $attempt->user_id->getValue(), 1 );
		$this->assertEquals( $attempt->status->getValue(), LoginAttempt::FailStatus );
	}

	public function testWithUnknownLogin() {
		try {
			UsersLogin::login( self::UnknownLogin, self::Password );
		}
		catch ( \NotFoundException $e ) {

		}
		$attempt = new LoginAttempt();
		$this->AssertTrue( $attempt->get( 1 ) );
		$this->assertEquals( $attempt->user_id->getValue(), 0 );
		$this->assertEquals( $attempt->status->getValue(), LoginAttempt::FailStatus );

	}

	public function testFirstLogin() {
		Helper::dbFixture( UsersLogin::LoginAttemptsTable,
						   array() );
		UsersLogin::login( self::Login, self::Password );;
		$user = UsersLogin::getCurrentSession();
		$info = new LoginInfo();
		$info->countForUser( $user );

		$fixture = array(
			'success' => array(
				'ip'     => null,
				'date'   => null,
				'method' => null,
			),
			'fail'    => array(
				'ip'    => null,
				'date'  => null,
				'count' => 0,
			)
		);

		$this->assertEquals( $fixture, $info->getViewData() );
	}

	public function testCountForUser() {
		$dates = array(
			date( 'Y-m-d H:i:s', strtotime( '-3 minute' ) ),
			date( 'Y-m-d H:i:s', strtotime( '-2 minute' ) ),
			date( 'Y-m-d H:i:s', strtotime( '-1 minute' ) ),
		);
		Helper::dbFixture( UsersLogin::LoginAttemptsTable,
						   array(
							   array(
								   'host'    => ip2long( self::SecondIpFixture ),
								   'date'    => $dates[ 0 ],
								   'status'  => LoginAttempt::SuccessStatus,
								   'user_id' => 1,
							   ),
							   array(
								   'host'    => ip2long( self::FirstIpFixture ),
								   'date'    => $dates[ 1 ],
								   'status'  => LoginAttempt::FailStatus,
								   'user_id' => 1,
							   ),
							   array(
								   'host'    => ip2long( self::FirstIpFixture ),
								   'date'    => $dates[ 2 ],
								   'status'  => LoginAttempt::FailStatus,
								   'user_id' => 1,
							   ),
						   ) );
		UsersLogin::login( self::Login, self::Password );;
		$user = UsersLogin::getCurrentSession();
		$info = new LoginInfo();
		$info->countForUser( $user );

		$fixture = array(
			'success' => array(
				'ip'     => self::SecondIpFixture,
				'date'   => $dates[ 0 ],
				'method' => '',
			),
			'fail'    => array(
				'ip'    => self::FirstIpFixture,
				'date'  => $dates[ 2 ],
				'count' => 2,
			)
		);
		$this->assertEquals( $fixture, $info->getViewData() );
	}

} 