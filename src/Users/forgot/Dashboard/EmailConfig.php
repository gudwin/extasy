<?php
namespace Extasy\Users\forgot\Dashboard {

    use \Sitemap_Sample;
    use \Sitemap_PageScriptConfig_Page;
    use \CConfig;
    use \UserAccount;
    use \UsersForgot;
    use \Extasy\Users\admin\ConfigPage;

    class EmailConfig extends ConfigPage
    {
        const scriptPath = 'Users/scripts/forgot.php';
        const scriptUrl = 'users/forgot_password/';

        const Title = 'Забытый пароль';

        protected $schemaName = UsersForgot::EmailConfigName;

        public static function startup()
        {
            $page = new EmailConfig();
            $page->process();
        }

        public static function install()
        {
            //
            $config = CConfig::createSchema(UsersForgot::EmailConfigName);
            $config->updateSchema(UsersForgot::EmailConfigName, 'Восстановление пароля пользователем');
            //
            $config->addControl('request_subject', 'inputfield', '', array(), 'subject');
            $config->addControl('request_content', 'htmlfield', '', array(), 'content');

            $config->addControl('newpassword_subject', 'inputfield', '', array(), 'subject');
            $config->addControl('newpassword_content', 'htmlfield', '', array(), 'content');

            $config->setTabsheets(
                array(
                    'Письмо: подтверждение смены' => array('request_subject', 'request_content'),
                    'Письмо: новый пароль' => array('newpassword_subject', 'newpassword_content'),
                )
            );
        }
    }

}