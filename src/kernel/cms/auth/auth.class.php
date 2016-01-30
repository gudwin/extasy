<?
use \Faid\DB, \Faid\UParser;

define( 'CMS_AUTH_TABLE', 'users' );
define( 'SESSION_AUTH', '_exst_class_auth' );

//

class CMSAuth {
	const EventName                   = 'CMS.Login';
	const AdministratorRoleName       = 'Administrator';
	const SystemAdministratorRoleName = 'System Administrator';
	const AuditorRoleName             = 'Auditor';
	const LogName                     = 'Dashboard.Login';
	private static $instance;
	private $login = '';
	private $password = '';
	/**
	 * Информация о текущем пользователей
	 * @var \UserAccount
	 */
	private $user = null;

	public static function getInstance() {
		if ( empty( self::$instance ) ) {
			new CMSAuth();
		}
		return self::$instance;
	}
    public static function install() {
        ACL::create( self::SystemAdministratorRoleName, 'System Administrator');
        ACL::create( self::AdministratorRoleName, 'Administrator');
    }

	public function __construct() {
		if ( empty( self::$instance ) ) {
			self::$instance = $this;
		}
		$this->extractAuthDataFromPost();
		$this->autoLogin();
	}

	protected function autoLogin() {
		if ( UsersLogin::isLogined() ) {

			$user = UsersLogin::getCurrentSession();
			if ( $this->isAdmin( $user ) ) {
				$this->user = $user;
			}

			EventController::callEvent( self::EventName, $this );
		}
	}

	public function check() {
        if ( !UsersLogin::isLogined() ) {
            $this->user = null;
        }
		$needAuth = empty( $this->user );
		if ( $needAuth ) {
			$authDataPresent = !empty( $this->login ) || !empty( $this->password ) ;
            try {
                if ( $authDataPresent || UsersLogin::isLogined()  ) {
                    if ( $authDataPresent ) {
                    	UsersLogin::login( $this->login, $this->password );                    		
                    }

                }
                $this->user = UsersLogin::getCurrentUser();
                $this->processUser();
            } catch (ForbiddenException $e ) {
                $this->authProc();
                return false;
            } catch ( \NotFoundException $e ) {
            	$this->authProc();
            	return false;
            }
		}
        return true;
	}

	/**
	 * Загружает из бд данные о пользователе и если он есть сохраняет в св-ве aUser
	 */
	public function processUser( ) {
		try {
            $notAuthorizedAction = empty($this->user ) || !$this->isAdmin( $this->user) ;
			if ( $notAuthorizedAction ) {
                UsersLogin::logout();
				throw new \ForbiddenException( 'Access denied. Only administrators & auditors permitted' );
			}
			EventController::callEvent( self::EventName, $this );
		}
		catch ( Exception $e ) {
			$this->user = null;
            throw $e;
		}
	}


	public function unAuthorize( $szUrl = '/' ) {
		$_SESSION[ SESSION_AUTH ] = array();
        if ( !headers_sent( )) {
            header( 'HTTP/1.0 401 Unauthorized' );
            header( 'Content-Type: text/html; charset= utf-8' );
            header( 'Location: ' . $szUrl );
        }
		UsersLogin::logout();
	}

	public function authProc() {
        if ( !headers_sent( )) {
            header( 'Content-Type: text/html; charset= utf-8' );
        }

		$register    = new SystemRegister( 'System/Front-end' );
		$techMessage = $register->technical_message->value;
		$template    = __DIR__ . DIRECTORY_SEPARATOR . 'login.tpl';
		//
		print UParser::parsePHPFile( $template,
									 array(
										 'techMessage' => $techMessage
									 ) );
	}

	public function isLogined() {
		return !empty( $this->user );
	}

	/**
	 * Проверяет, является ли текущий пользователь администратором
	 */
	public static function isAdmin( $user) {
		$rights = $user->rights->getValue();
		$result = !empty( $rights[ self::AdministratorRoleName ] )
			|| !empty( $rights[ self::SystemAdministratorRoleName ] )
			|| !empty( $rights[ self::AuditorRoleName ] );
		return $result;
	}

	public static function isSuperAdmin( UserAccount $user ) {
		$rights = $user->rights->getValue();
		return !empty( $rights[ self::SystemAdministratorRoleName ] );
	}
	public static function isAuditor( UserAccount $user ) {
		$rights = $user->rights->getValue();
		return !empty( $rights[ self::AuditorRoleName ] ) || self::isSuperAdmin( $user );
	}

	protected function extractAuthDataFromPost() {
		if ( !empty( $_POST[ 'login' ] )
			&& !empty( $_POST[ 'password' ] )
			&& !empty( $_POST[ 'cms_auth' ] )
		) {
			$this->login    = $_POST[ 'login' ];
			$this->password = $_POST[ 'password' ];
		}
	}
}

?>