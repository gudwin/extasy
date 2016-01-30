<?
namespace Extasy\Users\login\controller;

use \kcaptchaHelper;
use \UsersLogin;
use \EventController;
use \Exception;
use \Sitemap_Sample;
use \Extasy\Users\login\Dashboard\PageConfig;

//************************************************************//
//                                                            //
//              Контроллер авторизации                        //
//       Copyright (c) 2008 Ext-CMS (http://www.ext-cms.com/) //
//               отдел/сектор                                 //
//       Email:    info@gisma.ru (http://www.gisma.ru/)       //
//                                                            //
//  Разработчик: Gisma (26.01.2009)                           //
//  Модифицирован:  26.01.2009  by Gisma                      //
//                                                            //
//************************************************************//
use \SiteMapController;
class Login extends SiteMapController {
	const authFailed     = 'auth failed';
	const kcaptchaFailed = 'kcaptcha failed';
	protected $exception = false;
	public $layout = '';
	/**
	 * Page index where user will be redirected
	 * @var int
	 */
	protected $pageId = 0;

	public function __construct( $urlInfo = array() ) {
		parent::__construct( $urlInfo );
		$this->addPost( 'login,password,kcaptcha,remember', 'login' );
		$this->addPost( 'login,password,kcaptcha', 'login' );
		$this->addPost( 'login,password', 'login' );
		$this->addGet( 'logout', 'logout' );
		if ( !empty( $_REQUEST[ 'pageId' ] ) ) {
			$this->pageId = intval( $_REQUEST[ 'pageId' ] );
		}
	}

	public function main() {
		if ( UsersLogin::isLogined() ) {
			$this->jump( '/' );
		}
		require_once SETTINGS_PATH . 'users/login/form.cfg.php';
		$aParse = array(
			'aMeta'          => array(
				'title'       => PAGE_TITLE,
				'description' => PAGE_DESCRIPTION,
				'keywords'    => PAGE_KEYWORDS,
			),
			'loginException' => $this->exception,
			'page'           => $this->pageId,
			'backUrl'        => !empty( $_GET[ 'backUrl' ] ) ? htmlspecialchars( $_GET[ 'backUrl' ] ) : ''
		);
		$this->output( 'users/login/form', $aParse, array( 'page_with_bread_crumbs' ) );
	}

	public function login( $login, $password, $captchaCode = '', $remember = false ) {
		// fix for cases with captcha
		if ( !empty( $_REQUEST['remember'])) {
			$remember = $_REQUEST['remember'] == 'true';
		}
		// проверяем код капчи
		/**
		 * @todo Избавиться от этой зависимости
		 */
		require_once APPLICATION_PATH.'kcaptcha/helper.php';
		if ( !kcaptchaHelper::check( $captchaCode )) {
			$this->errorCode = self::kcaptchaFailed;
			return $this->main();
		}
		try {
			UsersLogin::login( $login, $password, $remember );

			EventController::callEvent( 'users_registration_after_login', UsersLogin::getCurrentSession() );
			$this->aParse[ 'loginSuccess' ] = true;
		}
		catch ( \Extasy\Users\login\UserNotConfirmedException $e ) {
			$this->jump('/signup/?code=');
		}
		catch ( Exception $e ) {
			\Extasy\Audit\Record::add( UsersLogin::LogName, $e->getMessage(), $e );
			$this->exception = $e;
			// Поддержка ajax-а 
			if ( empty( $_REQUEST[ 'ajaxRequest' ] ) ) {
				$this->main();
			}
		}
		// Если передавался параметр страниц
		if ( !empty( $this->pageId ) ) {
			$sitemap = Sitemap_Sample::get( $this->pageId );
			if ( !empty( $sitemap ) ) {
				$this->jump( $sitemap[ 'full_url' ] );
			}
		} elseif ( !empty( $_POST[ 'backUrl' ] ) ) {
			$backUrl = preg_replace( "#\n.*#", "", $_POST[ 'backUrl' ] );
			$this->jump( $backUrl );
		}

		// Поддержка аякса
		if ( !empty( $_REQUEST[ 'ajaxRequest' ] ) ) {
			$this->output( '/users/login/form' );
		}
		$this->jump( '/' );
	}

	public function logout() {
		try {
			UsersLogin::logout();
		}
		catch ( Exception $e ) {
		}
		if ( !empty( $_REQUEST[ 'ajaxRequest' ] ) ) {
			$this->aParse[ 'logoutSuccess' ] = true;
			$this->output( '' );
		}
		$this->jump( '/' );
	}

	protected function gotoConfirmationPage() {
		$sitemapInfo = Sitemap_Sample::getScript( 'users/scripts/signup.php' );
		$url         = sprintf( 'http:%s?code=', $sitemapInfo[ 'full_url' ] );
		$this->jump( $url );
	}

	public static function getUrl() {
		$sitemapInfo = Sitemap_Sample::getScriptByAdminInfo(PageConfig::scriptPath,PageConfig::scriptUrl);
		return $sitemapInfo['full_url'];
	}
}

?>