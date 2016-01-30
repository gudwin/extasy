<?php


namespace Extasy\Users\profile\Dashboard;

use \Extasy\Users\admin\ConfigPage;
use \Extasy\Users\UsersModule;
use \Extasy\Users\Api\DeleteProfile;

class DeleteProfileEmailConfig extends ConfigPage  {
	protected $schemaName = DeleteProfile::SchemaName;

	public static function install() {
		UsersModule::installEmail( DeleteProfile::SchemaName, 'Письмо удаления профиля');
	}

}