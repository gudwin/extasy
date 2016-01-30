<?
//************************************************************//
//                                                            //
//            Класс добавления новых документов               //
//       Copyright (c) 2008 Ext-CMS (http://www.ext-cms.com/) //
//               отдел/сектор                                 //
//       Email:    info@gisma.ru (http://www.gisma.ru/)       //
//                                                            //
//  Разработчик: Gisma (19.10.2008)                           //
//  Модифицирован:  19.10.2008  by Gisma                      //
//                                                            //
//************************************************************//
require_once LIB_PATH . 'kernel/functions/array.func.php';
class Sitemap_Controller_Add extends AdminPage
{
	protected $aOutput =array();
	public function __construct()
	{
		parent::__construct();
		$this->aOutput = array(
			'response' => '',
			'error'    => '',
		);
		$this->addPost('document_name,parent','post');
	}
	public function createDocument($document_name,$parent)
	{
		////////////////////////////////////////////////////////////////
		global $_BASE_DATA;
		$parent = intval($parent);

		$validator = new \Extasy\Validators\IsModelClassNameValidator( $document_name  );
		if ( !$validator->isValid() ) {
			throw new \ForbiddenException('Not a model');
		}
		$model = new $document_name();
		$model->createEmptyDocument($parent);
		//
		return $model->getSitemapId();
	}
	public function post($document_name,$parent)
	{
		try
		{
			$nResult = $this->createDocument($document_name,$parent);
		}
		catch (SiteMapException $e) {
			if (DEBUG == 1)
				$this->aOutput['error'] = $e->getMessage();
			else
				$this->aOutput['error'] = _msg('Ошибка при добавлении превью. Возможно документ не сущестует, либо такое превью уже существует');
			$this->output();
		}
		catch (Exception $e)
		{
			if (DEBUG == 1)
				$this->aOutput['error'] = $e->getMessage();
			else
				$this->aOutput['error'] = _msg('Документ "'.$document_name.'" не вернул все данные для создания превью');
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