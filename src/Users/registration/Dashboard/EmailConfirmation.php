<?php
namespace Extasy\Users\registration\Dashboard {

    use \Extasy\Users\admin\ConfigPage;
    use \Extasy\Users\UsersModule;
    use \UsersRegistration;

    class EmailConfirmation extends ConfigPage
    {
        const Title = 'Редактирование данных письма подтверждения регистрации';

        public function __construct()
        {
            $this->schemaName = UsersRegistration::RegistrationConfirmationConfigName;
            parent::__construct();
        }
        public static function install() {
            UsersModule::installEmail( UsersRegistration::RegistrationConfirmationConfigName, self::Title);
        }
        public static function uninstall() {
            UsersModule::uninstallEmail( UsersRegistration::RegistrationConfirmationConfigName);
        }
    }
}
?>