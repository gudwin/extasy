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
use \SiteMapController;
class UsersLogin_NeedAuthController extends SiteMapController
{
	protected $aProfile = null;
	public function __construct( $urlInfo = array())
	{
		parent::__construct( $urlInfo );

		if (!UsersLogin::isLogined())
		{
			/**
			 * @todo Избавиться от этой зависимости
			 */
			$this->addAlert(\plugins\MessageDictionary\Plugin::getMessage('need_auth'));
			$this->jump('/');
			throw new Exception ('User not logined');
		}
		$this->aProfile = UsersLogin::getCurrentSession();

	}
}
?>