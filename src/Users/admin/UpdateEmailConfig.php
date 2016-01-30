<?php
namespace Extasy\Users\admin;

use \UserAccount;
use \Extasy\Users\admin\ConfigPage;
use \CConfig;
use \Extasy\Users\UsersModule;
class UpdateEmailConfig extends ConfigPage {
	protected $schemaName = UserAccount::UpdateEmailConfigName;

	public static function install() {
		UsersModule::installEmail( UserAccount::UpdateEmailConfigName,'Смена e-mail пользователем' );
	}
}