<?php


namespace Extasy\tests\acl;

use \Extasy\Api\Exception;
use \Extasy\Columns\Password as passwordColumn;
use \Extasy\tests\Helper as TestsHelper;
use \ACL;
use \Extasy\acl\ACLUser;
use \UserAccount;
use \UsersLogin;
use \Faid\SimpleCache;

class ControllerTest extends \Extasy\tests\Helper {
	const passwordFixture = '12345678';
	const loginFixture    = 'testUser';
	const testReadRight   = 'Read.smth';
	const testWriteRight  = 'Write.smth';

	public function setUp() {
		parent::setUp();
		include __DIR__ . DIRECTORY_SEPARATOR . 'import.php';
		TestsHelper::dbFixture( USERS_TABLE,
								array( array(
										   'login'    => self::loginFixture,
										   'email'    => 'test@test.com',
										   'password' => PasswordColumn::hash( self::passwordFixture )
									   ),
									   array(
										   'login'    => 'guest',
										   'email'    => 'guest@guest.com',
										   'password' => PasswordColumn::hash( self::passwordFixture )
									   ) ) );

		ACL::create( self::testReadRight, '' );
		ACL::create( self::testWriteRight, '' );

		try {
			SimpleCache::clear( ACLUser::CacheKey );
		}
		catch ( \Exception $e ) {
		}
	}

	public function tearDown() {
		parent::tearDown();
		if ( UsersLogin::isLogined() ) {
			UsersLogin::logout();
		}
	}

	public function testControllerWorksWithoutUserRights() {
		$controller = new TestController();
		$controller->process();
		$this->assertTrue( $controller->isMainCalled() );
	}

	/**
	 * @expectedException ACLException
	 */
	public function testOnlyArrayAllowedInControllerGrantList() {
		$controller = new TestController();
		$controller->setRequiredRights( self::testReadRight );
		$controller->process();
	}

	/**
	 * @expectedException ForbiddenException
	 */
	public function testUserHasNoRights() {
		$controller = new TestController();
		$controller->setRequiredRights( array( self::testReadRight ) );
		$controller->process();

		$this->assertTrue( false );
	}

	/**
	 * @expectedException \ForbiddenException
	 */
	public function testAllRightsChecked() {
		$user = \UserAccount::getByLogin( self::loginFixture );
		Acl::grant( self::testReadRight, $user->obj_rights->getEntity() );
		UsersLogin::login( self::loginFixture, self::passwordFixture );
		//
		$controller = new TestController();
		$controller->setRequiredRights( array( self::testReadRight, self::testWriteRight ) );
		$controller->process();

	}

	public function testGuestHasRights() {
		$guest = UserAccount::getByLogin( 'guest' );
		Acl::grant( self::testReadRight, $guest->obj_rights->getEntity() );
		UsersLogin::login( self::loginFixture, self::passwordFixture );
		//
		$controller = new TestController();
		$controller->setRequiredRights( array( self::testReadRight ) );
		$controller->process();
		$this->assertTrue( $controller->isMainCalled() );
	}

	public function testUserHasRights() {
		$user = UserAccount::getByLogin( self::loginFixture );
		Acl::grant( self::testReadRight, $user->obj_rights->getEntity() );
		Acl::grant( self::testWriteRight, $user->obj_rights->getEntity() );
		UsersLogin::login( self::loginFixture, self::passwordFixture );
		//
		$controller = new TestController();
		$controller->setRequiredRights( array( self::testReadRight, self::testWriteRight ) );
		$controller->process();
		$this->assertTrue( $controller->isMainCalled() );

	}

	public function testUserAndGuestRightsMerged() {
		$user  = UserAccount::getByLogin( self::loginFixture );
		$guest = UserAccount::getByLogin( 'guest' );
		Acl::grant( self::testReadRight, $guest->obj_rights->getEntity() );
		Acl::grant( self::testWriteRight, $user->obj_rights->getEntity() );
		UsersLogin::login( self::loginFixture, self::passwordFixture );
		//
		$controller = new TestController();
		$controller->setRequiredRights( array( self::testReadRight, self::testWriteRight ) );
		$controller->process();
		$this->assertTrue( $controller->isMainCalled() );

	}

	public function testGuestCacheGeneratedOnUserUpdate() {
		$user  = UserAccount::getByLogin( 'guest' );
		$paths = array(
			self::testReadRight  => true,
			self::testWriteRight => true
		);
		$user->obj_rights->setValue( $paths );
		// test that cache file not exists
		try {
			SimpleCache::get( ACLUser::CacheKey );
			$this->AssertTrue( false, 'seems like cache exists' );
		}
		catch ( \Exception $e ) {

		}
		$user->update();
		$result = SimpleCache::get( ACLUser::CacheKey );
		//
		$this->assertTrue( in_array( self::testReadRight, $result ) );
		$this->assertTrue( in_array( self::testWriteRight, $result ) );

	}
} 