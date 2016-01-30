<?php
namespace Extasy\Users\profile\Dashboard {
	use \Extasy\Users\admin\ConfigPage;
	use \Extasy\Users\profile\ProfileController;
	use \Extasy\Users\UsersModule;

	class PageConfig extends ConfigPage {
		const SchemaName = 'users.profilePage';
		const scriptPath = 'users/scripts/profile.php';
		const scriptUrl = 'users/profile/index';
		public function __construct()
		{
			$this->schemaName = ProfileController::PageConfigName;
			parent::__construct();
		}

		public static function startup() {
			$page = new PageConfig();
			$page->process();
		}
		public static function install() {
			UsersModule::installPage( self::SchemaName, self::scriptPath, 'profile', self::scriptUrl, 'Страница профиля' ) ;
		}
	}
}
