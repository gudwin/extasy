<?
//              Страница отсылки пароля                       //
//       Copyright (c) 2008 Ext-CMS (http://www.ext-cms.com/) //
//               отдел/сектор                                 //
//       Email:    info@gisma.ru (http://www.gisma.ru/)       //
//                                                            //
//  Разработчик: Gisma (26.01.2009)                           //
use \SiteMapController;
class UsersForgot_Send extends SiteMapController {
	protected $layout = 'layout/default';

	public function __construct( $urlInfo = array() ) {
		parent::__construct( $urlInfo );
		$this->addPost( 'email', 'send' );
		$this->addGet( 'id,code', 'resetPassword' );

	}

	public function main( $error = '' ) {
	}
	public function resetPassword( $userId, $code ) {
		$user = UserAccount::getById( $userId  );

		if ( $user->password->getValue() == $code ) {
			UsersForgot::resetPassword( $user );
			$this->addAlert( 'Пароль выслан');
 		} else {
			$this->addAlert( 'Токен не найден');
		}


		$this->jump('/');
	}
	public function send( $email ) {

		try {
			$aParse[ 'email' ] = htmlspecialchars( $email );
			UsersForgot::send( $email );
		}
		catch ( Exception $e ) {
			CMSLog::addMessage( 'users', $e );
			if ( !empty( $_REQUEST[ 'ajaxRequest' ] ) ) {
				$this->aParse[ 'forgotFailed' ] = true;
				$this->output( '' );
			} else {
				/**
				 * @todo Избавиться от этой зависимости
				 */
				$this->addAlert( \plugins\MessageDictionary\Plugin::getMessage('users.forgot.accountNotFound') );
				$this->jumpBack();
			}

		}
		if ( !empty( $_REQUEST[ 'ajaxRequest' ] ) ) {
			$this->aParse[ 'forgotSuccess' ] = true;
			$this->output( );
		} else {
			/**
			 * @todo Избавиться от этой зависимости
			 */
			$this->addAlert( \plugins\MessageDictionary\Plugin::getMessage('users.forgot.sent') );
			$this->jumpBack();
		}

	}
}

?>