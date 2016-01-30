<?
//************************************************************//
//                                                            //
//            Класс удаления документов                       //
//       Copyright (c) 2008 Ext-CMS (http://www.ext-cms.com/) //
//               отдел/сектор                                 //
//       Email:    info@gisma.ru (http://www.gisma.ru/)       //
//                                                            //
//  Разработчик: Gisma (19.10.2008)                           //
//  Модифицирован:  19.10.2008  by Gisma                      //
//                                                            //
//************************************************************//

class Sitemap_Controller_Delete extends extasyPage
{
	protected $aOutput = array();
	public function __construct()
	{
		parent::__construct();
		$this->addPost('document_name,document_id','delete');
	}
	protected function delete($szDocument,$nId)
	{
		$this->aOutput['reponse'] = '';
		$this->aOutput['error'] = '';
		try
		{
			SiteMap::removeDocument($szDocument,$nId);
			$this->aOutput['response'] = _msg('Документ успешно удален');
		}
		catch (SitemapException $e)
		{
			$this->aOutput['error'] = _msg('Ошибка при удалении. Возможно документ не поддерживается, либо уже удален');
		}
		$this->output();
	}
	public function output()
	{
		print json_encode($this->aOutput);
		Trace::setDisabled();
		die();
	}
}

?>