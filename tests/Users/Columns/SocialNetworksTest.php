<?php

namespace Extasy\tests\Users\Columns;

use \Extasy\tests\Helper;
use \Extasy\Users\Columns\SocialNetworks;
use Faid\DBSimple;

class SocialNetworksTest extends \Extasy\tests\Users\UsersTest {
	const SecondLogin      = 'my_login';
	const UID = '123456';
	/**
	 * @var \Extasy\Users\Columns\SocialNetworks
	 */
	protected $column = null;
	/**
	 * @var \UserAccount
	 */
	protected $user = null;

	public function setUp() {
		parent::setUp();

		Helper::setupUsers( array( array(
									   'login'    => self::Login,
									   'password' => self::Password,
									   'email'    => self::Email
								   ),
								   array(
									   'login'    => self::SecondLogin,
									   'password' => self::Password,
									   'email'    => self::Email
								   )
							) );

//		SocialNetworks::createTables();

		Helper::dbFixtures( [
								\Extasy\Users\Social\Network::TableName => [
									[ 'name' => 'facebook' ],
									[ 'name' => 'vkontakte' ],
								],
								SocialNetworks::UIDTable   => [
									[ 'user_id' => 1, 'network_id' => 1, 'uid' => self::UID ]
								]
							] );


	}

	/**
	 * @expectedException \Extasy\Users\Columns\SocialNetworksException
	 */
	public function testGetByUknownUID() {
		SocialNetworks::getByUID( 'some_unknown','facebook');
	}
	public function testGetByUID() {
		$user = SocialNetworks::getByUID( self::UID, 'facebook');
		$this->assertTrue( is_object( $user ));
		$this->assertTrue( $user instanceof \UserAccount);

		$this->assertEquals( $user->login->getValue(), self::Login );
	}

	public function testGetValue() {
		$this->factoryColumn( self::Login );
		$this->column->onAfterSelect([]);

		$result = $this->column->getValue();
		$this->assertTrue( is_array( $result ));
		$this->assertEquals( self::UID, $result['facebook']);
	}

	/**
	 * @expectedException \Extasy\Users\Columns\SocialNetworksException
	 */
	public function testTokenAlreadyUsed() {
		$this->factoryColumn( self::SecondLogin );
		//
		$this->column->setValue( [
							   'facebook' => self::UID
						   ] );
		$this->column->onUpdate( new \Extasy\ORM\QueryBuilder( 'select' ) );
	}
	public function testReSave() {
		$this->factoryColumn( self::Login);
		$this->column->setValue( [
									 'facebook' => self::UID
								 ] );
		$this->column->onUpdate( new \Extasy\ORM\QueryBuilder( 'select' ));
		//
		$this->assertEquals( 1 , DBSimple::getRowsCount( SocialNetworks::UIDTable ));

	}
	public function testStored() {
		$fixture = [
			'facebook'  => 'my_token',
			'vkontakte' => 'second_token'
		];

		$this->factoryColumn( self::SecondLogin );

		$this->column->setValue( $fixture  );
		$this->column->onUpdate( new \Extasy\ORM\QueryBuilder( 'select' ) );
		//
		$this->factoryColumn( self::SecondLogin );
		$this->column->onAfterSelect( [] );
		//
		$this->assertEquals( $this->column->getValue(), $fixture );

	}
	protected function factoryColumn( $userLogin ) {
		$user = \UserAccount::getByLogin( $userLogin );
		$this->column = new SocialNetworks( 'testName', [ ], null );
		$this->column->setDocument( $user );
		return $this->column ;
	}


} 