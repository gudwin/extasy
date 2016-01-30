<?php
namespace Extasy\Users\forgot\Dashboard {
	use \Sitemap_Sample;
	use \Sitemap;
	use \CConfig;
	use \Extasy\Users\admin\ConfigPage;

	class PageConfig extends ConfigPage {

		const ConfigName = 'users.forgotPage';
		protected $schemaName = self::ConfigName;

		const scriptPath     = 'users/scripts/forgot.php';
		const scriptAdminUrl = 'users/forgot_password/index';

		public static function startUp() {
			$page = new PageConfig();
			$page->process();
		}
		public static function install() {
			$sitemapInfo = Sitemap_Sample::getScriptByAdminInfo(self::scriptPath,self::scriptAdminUrl);
			if ( empty( $sitemapInfo )) {
				Sitemap::addScript( 'Забытый пароль', self::scriptPath, 'forgot',0, self::scriptAdminUrl );
			}
			//
			$config = CConfig::createSchema( self::ConfigName );
			$config->updateSchema(self::ConfigName, 'Страница авторизации');
			$config->addControl('seo_title', 'inputfield', 'SEO=title');
			$config->addControl('seo_keywords', 'inputfield', 'SEO=keywords', array('rows' => 10));
			$config->addControl('seo_description', 'inputfield', 'SEO=description', array('rows' => 10));
			$config->setTabsheets(
				array(
					'SEO' => array('seo_title', 'seo_keywords', 'seo_description')
				)
			);
		}
	}

}