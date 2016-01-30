<?php
namespace Extasy\Users\login\Dashboard {

    use \Sitemap_PageScriptConfig_Page;
    use \Sitemap_Sample;
    use \UsersLogin;
    use \Extasy\Users\UsersModule;

    class PageConfig extends Sitemap_PageScriptConfig_Page
    {
        const path = 'users/login/form.cfg.php';
        const Title = 'Страница авторизации';
        const scriptPath = 'users/scripts/login.php';
        const scriptUrl = 'users/login/index';

        public static function startup()
        {
            $sitemapInfo = Sitemap_Sample::getScriptByAdminInfo(self::scriptPath, self::scriptUrl);
            $page = new PageConfig($sitemapInfo['id'], SETTINGS_PATH . self::path);
            $page->process();
        }


        public static function install()
        {
            UsersModule::installPage(UsersLogin::SchemaName, self::scriptPath, 'login', self::scriptUrl, self::Title);
        }
    }
}
