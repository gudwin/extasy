<?php
namespace Extasy\tests\acl;

use \Extasy\tests\BaseTest;
use \ACL;
use \Extasy\tests\Helper;
use \UserAccount;
use \UsersLogin;
use \Extasy\acl\ModelHelper;
use \Extasy\Columns\Password as passwordColumn;

class ModelHelperTest extends BaseTest {
	const LoginFixture = 'root';
	const PasswordFixture = 'password';
	public function setUp( ) {
		include __DIR__ . DIRECTORY_SEPARATOR . 'import.php';

		ACL::create( TestModel::PermissionName, '');
		Helper::dbFixture( UserAccount::getTableName(), array(
			array(
				'login' => self::LoginFixture,
				'password' => passwordColumn::hash( self::PasswordFixture )
			)
		));
		$user = UserAccount::getById( 1 ) ;
		ACL::grant( TestModel::PermissionName, $user->rights->getEntity() );
	}
	public function tearDown( ) {
		if ( UsersLogin::isLogined() ) {
			UsersLogin::logout( );
		}
	}

	/**
	 * @expectedException \NotFoundException
	 */
	public function testIfUnknownDocumentPresent( ) {
		ModelHelper::isEditable( 'some_unknown_document' );
	}
	public function testIfDocumentEditable( ) {
		$this->AssertFalse( ModelHelper::isEditable( TestModel::ModelName ));
	}
	public function testIfDocumentNonEditable( ) {
		UsersLogin::login( self::LoginFixture, self::PasswordFixture );
		$this->AssertTrue( ModelHelper::isEditable( TestModel::ModelName ));
	}
	public function testThatIsEditableSupportsObjects( ) {
		$this->AssertFalse( ModelHelper::isEditable( new TestModel() ) );
		UsersLogin::login( self::LoginFixture, self::PasswordFixture );
		$this->AssertTrue( ModelHelper::isEditable( new TestModel ) );
	}
}