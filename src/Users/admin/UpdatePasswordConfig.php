<?php
namespace Extasy\Users\admin;

use \UserAccount;
use \Extasy\Users\admin\ConfigPage;
use \CConfig;
use \Extasy\Users\UsersModule;

class UpdatePasswordConfig extends ConfigPage {
	protected $schemaName = UserAccount::UpdatePasswordConfigName;

	public static function install() {
		UsersModule::installEmail( UserAccount::UpdatePasswordConfigName, 'Смена пароля пользоваетелем пользователем');
	}
}