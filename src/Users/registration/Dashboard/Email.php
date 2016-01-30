<?php
namespace Extasy\Users\registration\Dashboard {
	use \Extasy\Users\admin\ConfigPage;
	use \Extasy\Users\UsersModule;
	use \UsersRegistration;
	class Email extends ConfigPage {
		const Title = 'Редактирование данных письма регистрации';
		public function __construct() {
			$this->schemaName =  UsersRegistration::RegistrationAcceptedConfigName;
			parent::__construct();
		}
		public static function install() {
			UsersModule::installEmail( UsersRegistration::RegistrationAcceptedConfigName, self::Title);
		}
		public static function uninstall() {
			UsersModule::uninstallEmail( UsersRegistration::RegistrationAcceptedConfigName);
		}
	}

}
?>