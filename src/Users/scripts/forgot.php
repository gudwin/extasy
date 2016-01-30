<?
//************************************************************//
//                                                            //
//                     Заголовок                              //
//       Copyright (c) 2008 Ext-CMS (http://www.ext-cms.com/) //
//               отдел/сектор                                 //
//       Email:    info@gisma.ru (http://www.gisma.ru/)       //
//                                                            //
//  Разработчик: Gisma (          )                           //
//  Модифицирован:  Дата мод.   by Gisma                      //
//                                                            //
//************************************************************//
require_once SETTINGS_PATH.'users/forgot/form.cfg.php';


class FORGOTPage extends UsersForgot_Send
{
	public function main($error = '')
	{
		$aParse = array(
			'aMeta' => array(
				'title' => FORGOT_SEO_TITLE,
				'keywords' => FORGOT_SEO_KEYWORDS,
				'description' => FORGOT_SEO_DESCRIPTION,
			),
			);

		//
		$this->output('users/forgot/form',$aParse, array( 'page_with_bread_crumbs' ));
	}
}
return new FORGOTPage(\Extasy\sitemap\Route::getCurrentUrlInfo());

?>