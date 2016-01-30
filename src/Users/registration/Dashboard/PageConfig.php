<?php
namespace Extasy\Users\registration\Dashboard {
    use \UsersRegistration;
    use \Extasy\Users\admin\ConfigPage;
    use \Extasy\Users\UsersModule;

    class PageConfig extends ConfigPage
    {
        const Title = 'Страница регистрации';
        const scriptPath = 'users/scripts/signup.php';
        const scriptAdminUrl = 'users/registration/index';

        public function __construct()
        {
            $this->schemaName = UsersRegistration::RegistrationPageConfigName;
            parent::__construct();
        }

        public static function startUp()
        {
            $page = new PageConfig();
            $page->process();
        }

        public static function install()
        {
            UsersModule::installPage(UsersRegistration::RegistrationPageConfigName, self::scriptPath, 'signup',
                self::scriptAdminUrl, self::Title);
        }
    }

}
?>