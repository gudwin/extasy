<?
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

namespace Extasy\Users\login\controller;

use \UsersLogin;
use \SiteMapController;

class AuthorizationRequired extends SiteMapController
{
    const MessageKey = 'users.AutorizationRequired';
	protected $aProfile = null;
	public function __construct( $urlInfo = array())
	{
		parent::__construct( $urlInfo );

		if (!UsersLogin::isLogined())
		{
			/**
			 * @todo Исправить проблему с зависимостью от MessageDictionary
			 */
            $this->addError(\plugins\MessageDictionary\Plugin::getMessage(self::MessageKey));
            $this->jump( Login::getUrl() );
		}
		$this->reloadCurrentSession();

	}
	protected function reloadCurrentSession() {
		$this->aProfile = UsersLogin::getCurrentSession();
	}
}
?>