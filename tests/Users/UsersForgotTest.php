<?php
namespace Extasy\tests\Users {
	class UsersForgotTest extends UsersTest {
		/**
		 * @expectedException \NotFoundException
		 */
		public function testForgotUnknownUser() {
			//
			\UsersForgot::send( 'yyy@yy.com');
		}
		/**
		 * @expectedException \Extasy\Users\login\LoginAttemptsException
		 */
		public function testForgotUserSessionLimit() {
			self::configureLoginAttempts(0, 10);
			\UsersForgot::send( self::Email );

		}
		/**
		 * @expectedException \Extasy\Users\login\LoginAttemptsException
		 */
		public function testForgotUserHostLimit() {
			self::configureLoginAttempts(10, 0);
			\UsersForgot::send( self::Email );

		}
	}
}