<?
use \CMSLog;
use \Extasy\Audit\Record;
use \Faid\DB;
use \Faid\DBSimple;
use \Extasy\Columns\Password as passwordColumn;
use \Extasy\Users\login\LoginAttemptsException;
use \Faid\Configure\Configure;

//************************************************************//
//                                                            //
//           Класс авторизации пользователей на сайте         //
//       Copyright (c) 2008 Ext-CMS (http://www.ext-cms.com/) //
//               отдел/сектор                                 //
//       Email:    info@gisma.ru (http://www.gisma.ru/)       //
//                                                            //
//  Разработчик: Gisma (25.01.2009)                           //
//  Модифицирован:  25.01.2009  by Gisma                      //
//                                                            //
//************************************************************//
// 2010.09.24 - Добавил шаблонную функцию	
//************************************************************//
class UsersLogin {
	const RememberInterval = 7;
	const LogName          = 'Users.login';

	const LoginAttemptsTable = 'login_attempts';

	const LogSessionKey = 'DashboardLoginAttempts';


	const cookieName = 'usp4';

	const SystemRegisterPath = '/System/Security/LoginAttempts';

	const SchemaName = 'users.loginPage';

	protected static $sessionKey = 'userslogin';

	protected static $session = null;


	protected static $currentUser = null;

	protected static $perSession = 0;
	protected static $perHost = 0;

	/**
	 * Возвращает данны для вывода информации о пользователе  на странице
	 */
	public static function getCurrentUserParseData() {
		$result = array();

		if ( self::isLogined() ) {
			$user                    = self::getCurrentSession();
			$result[ 'currentUser' ] = $user->getParseData();
		}

		return $result;
	}

	/**
	 *
	 * @return boolean
	 */
	public static function isLogined() {
		static $isFirstCall = true;
		if ( $isFirstCall ) {
			$isFirstCall = false;
			self::autoloadFromSession();
            if ( empty( self::$session )) {
                self::autoloadFromCookie();
            }
		}
		if ( !empty( self::$session ) ) {
			return true;
		} else {
			return false;
		}
	}

	public static function autoLoadFromCookie() {
		$isCookieSet = !empty ( $_COOKIE[ self::cookieName ] );
		if ( !$isCookieSet ) {
			return;
		}

		$passwordHash = $_COOKIE[ self::cookieName ];
		// Запрос провещяюрий есть ли юзер или нет и не забанен ли он
		$sql = 'SELECT * FROM `%s` WHERE LENGTH(`confirmation_code`) = 0 and md5(CONCAT(`password`,"%s",`id`)) = "%s"';
		$sql = sprintf( $sql,
						USERS_TABLE,
						DB::escape( \Faid\Configure\Configure::read( \Extasy\CMS::SaltConfigureKey ) ),
						DB::escape( $passwordHash ) );
		//
		$aFound = DB::get( $sql );
		if ( empty( $aFound ) ) {
			return false;
		}
		try {
			self::forceLogin( new UserAccount( $aFound ) );
			return true;
		}
		catch ( Exception $e ) {
			// Игнорируем случай неверной аутентификации
		}

		return false;

	}

	/**
	 * Первично инициализируем сессию и обнуляем её
	 */
	public static function autoLoadFromSession() {
		self::$session = null;

		if ( !isset( $_SESSION ) ) {
			$_SESSION = array();
		}
		// initialize session 
		if ( !isset( $_SESSION[ self::$sessionKey ] ) ) {
			$_SESSION[ self::$sessionKey ] = null;
		}
		self::$session = & $_SESSION[ self::$sessionKey ];
	}

	public static function forceLogin( $login ) {
		if ( !is_object( $login ) ) {
			$user = UserAccount::getByLogin( $login );
		} else {
			$user = $login;
		}
		\Extasy\Users\login\LoginAttempt::quickRegister( $user, true );
		$user->updateLastActivityDate();
		self::setupSession( $user->getData() );
	}

	public static function login( $login, $password, $remember = false ) {
		CMSLog::addMessage( self::LogName, sprintf( 'Login attempt into "%s"', $login ) );
		try {
            UsersLogin::testLoginAttempts();
		}
		catch ( \Extasy\Users\login\LoginAttemptsException $e ) {
			CMSLog::addMessage( __CLASS__, sprintf( 'Login attempts limit exceeded' ) );
			CMSLog::addMessage( __CLASS__, $e );
			try {
				$user = UserAccount::getByLogin( $login );
				$e->blockUser( $user );

				throw $e;
			}
			catch ( \NotFoundException $e ) {

			}
			return;

		}

		try {
			$user = UserAccount::getByLogin( $login );
			self::isAuthPassed( $user, $password );
			\Extasy\Users\login\LoginAttempt::quickRegister( $user, true );
		}
		catch ( \Exception $e ) {
			\Extasy\Users\login\LoginAttempt::quickRegister( !empty( $user ) ? $user : null, false );
			throw $e;
		}
		self::testConfirmationCode( $user );
		$user->updateLastActivityDate();

		// write to session login
		self::setupSession( $user->getData(), $remember );
		//
		self::$currentUser = $user;

		//
		CMSLog::addMessage( self::LogName, sprintf( 'User identified as "%s"', $login ) );
		// Вызываем обработку события, на вход в каждый перехватчик передается текущий объект пользователя
		EventController::callEvent( 'users_after_login', $user );


	}

	public static function testConfirmationCode( $user ) {
		if ( strlen( $user->confirmation_code->getValue() ) == 0 ) {
			return true;
		} else {
			throw new \Extasy\Users\login\UserNotConfirmedException( 'User not allowed to login. Confirm your registration, please' );
		}
	}


	/**
	 * @param $user
	 * @param $password
	 *
	 * @return bool
	 */
	protected static function isAuthPassed( $user, $password ) {

		$authPassed = $user->password->getValue() == PasswordColumn::hash( $password );

		if ( !$authPassed ) {
			// clean up session
			self::unsetSession();
			$error = sprintf( 'Password incorrect. Login attempt into user account "%s" ', $user->login );
			throw new \ForbiddenException( $error );
		}
		$user->time_access->onLogin();
		return $authPassed;
	}

	/**
	 *
	 */
	public static function logout() {
		if ( self::isLogined() ) {
			EventController::callEvent( 'users_after_logout' );
			try {
				$user = self::getCurrentUser();
				CMSLog::addMessage( __CLASS__, sprintf( 'User `%s` logged out', $user->login->getValue()) );
			} catch (\Exception $e ) {
				$short = 'Failed to logout user. Probably, there is an issue inside User Sesison';
				$full = sprintf("%s\r\n%s", $short, $e );
				Record::add(__CLASS__, $short, $full );
			}
		}
		self::unsetSession();
		self::$currentUser = null;
	}


	public static function getCurrentUser() {
		$result = self::getCurrentSession();
		if ( is_object( $result ) ) {
			return $result;
		} else {
			throw new ForbiddenException( 'Access denied. Only for authorized persons' );
		}
	}

	/**
	 * @return UserAccount
	 */
	public static function getCurrentSession() {
		if ( !empty( self::$currentUser ) ) {
			return self::$currentUser;
		} elseif ( !empty( self::$session ) ) {
			try {
				self::$currentUser = UserAccount::getById( self::$session[ 'id' ] );
			}
			catch ( \NotFoundException $e ) {
				self::unsetSession();
				throw $e;
			}

			return self::$currentUser;
		}
		return null;
	}

	protected static function setupSession( $row, $setRememberCookie = false ) {
		self::$session = array(
			'id'    => $row[ 'id' ],
			'login' => $row[ 'login' ],
		);
		$domain = Configure::read( \Extasy\CMS::MainDomainConfigureKey );
		$domain = '.'.$domain;
		if ( $setRememberCookie ) {
			$value = join( '',
						   [ $row[ 'password' ],
							 Configure::read( \Extasy\CMS::SaltConfigureKey ),
							 $row[ 'id' ]
						   ] );
			$value = md5( $value );

			self::setCookie( $value, time() + self::RememberInterval * 86400);
		} else {
			// Посылаем кукисы для очистки пароля

		}
	}
	protected static function setCookie( $value, $time  ) {
		$domain = Configure::read( \Extasy\CMS::MainDomainConfigureKey );
		$domain = '.'.$domain;
		setcookie( self::cookieName, $value , $time , '/' , $domain);
	}
	protected static function unsetSession() {
		// обнуляем сессию
		self::$session = null;
		//
		if ( headers_sent() ) {
			return;
		}
		self::setCookie( '',0 );


		\Extasy\Users\Social\FacebookApi::cleanUpSession();
		\Extasy\Users\Social\TwitterApi::cleanUpSession();
		\Extasy\Users\Social\VkontakteApi::cleanUpSession();
		\Extasy\Users\Social\OdnoklassnikiApi::cleanUpSession();
	}

	protected static function reloadAttemptsConfig() {
		$register         = new SystemRegister( self::SystemRegisterPath );
		self::$perHost    = intval( $register->PerHost->value );
		self::$perSession = intval( $register->PerSession->value );
	}


	/**
	 *
	 */
	public static function testLoginAttempts() {
		self::reloadAttemptsConfig();
		//
		$host = !empty( $_SERVER[ 'REMOTE_ADDR' ] ) ? $_SERVER[ 'REMOTE_ADDR' ] : '127.0.0.1';


		// Check that per-host limit not exceeded
		$perHostAttempts = 1 + DBSimple::getRowsCount(
									   self::LoginAttemptsTable,
									   array(
										   'host' => ip2long( $host ),
										   'date > NOW() - INTERVAL 1 hour'
									   )
			);

		// Check that per-session limit not exceeded
		if ( empty( $_SESSION[ self::LogSessionKey ] ) ) {
			$_SESSION[ self::LogSessionKey ] = 1;
		} else {
			$_SESSION[ self::LogSessionKey ]++;
		}
		$perSessionAttempts = $_SESSION[ self::LogSessionKey ];

		if ( $perHostAttempts > self::$perHost ) {
			$error = sprintf( 'Per host login/forgot password attempts exceeded. Current host count: %d, limit: %d ',
							  $perHostAttempts,
							  self::$perHost
			);
			throw new LoginAttemptsException( $error );
		}
		//
		if ( $perSessionAttempts > self::$perSession ) {
			throw new LoginAttemptsException( sprintf( 'Per session login/forgot password attempts exceeded %d:%d',
													   $perSessionAttempts,
													   self::$perSession ) );
		}
	}

}

?>