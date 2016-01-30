<?php
namespace Extasy\tests\Menu;

use \Extasy\Columns\Password as passwordColumn;
use \ACL;
use \UserAccount;
use \Extasy\tests\system_register\Restorator;
use \Extasy\tests\Helper as TestsHelper;

class MenuFixtures {
	const rightFixture = 'testRight';

	const loginFixture = 'login';

	const passwordFixture = 'password';

	public static function run() {
		TestsHelper::dbFixture( ACL_TABLE, array() );
		Restorator::restore();
		TestsHelper::dbFixture(
				   USERS_TABLE,
				   array(
					   array(
						   'login'    => self::loginFixture,
						   'password' => passwordColumn::hash( self::passwordFixture )
					   )
				   )
		);
		//
		ACL::create( self::rightFixture );
		//
		$user = UserAccount::getByLogin( self::loginFixture );
		ACL::grant( self::rightFixture, $user->obj_rights->getEntity() );
	}
}