<?
use \Faid\UParser as UParser;
//************************************************************//
//                                                            //
//            Класс сортировки дочерних документов            //
//       Copyright (c) 2008 Ext-CMS (http://www.ext-cms.com/) //
//               отдел/сектор                                 //
//       Email:    info@gisma.ru (http://www.gisma.ru/)       //
//                                                            //
//  Разработчик: Gisma (19.10.2008)                           //
//  Модифицирован:  26.10.2008  by Gisma                      //
//                                                            //
//************************************************************//
//  Собран движок                                             //
//************************************************************//

class Sitemap_Controller_Order extends AdminPage
{
	protected $szTitle = '';
	protected $aBegin = array();
	protected $back = '';
	protected $nId = 0;
	public function __construct()
	{

		parent::__construct();
		$this->addPost('id,order_value','post');
		$this->addGet('id','show');
	}
	public function show($id)
	{
		// Получаем ряд
		$id = intval($id);
		$this->nId = $id;
		try {
			// Получаем детей
			$aChild = Sitemap::selectChild($id);
			if ($id != 0)
			{
				$aRow = Sitemap_Sample::get($id);
			} else
			{
				$aRow = null;
			}
			/*$aData = array();
			foreach ($aChild as $row)
			{
				$aData[] = $row['id'];
			}*/
			$aData = $aChild;

			//
			$this->formatDesign($aRow);
			// Выводим форму сортировки
			print UParser::parsePHPFile(LIB_PATH.'sitemap/controller/tpl/order.tpl',array(
				'szTitle' => $this->szTitle,
				'aBegin'  => $this->aBegin,
				'id'    => $id,
				'back'    => $this->back,
				'aData'   => $aData,
			));
			$this->output();

		}
		catch (SiteMapException $e)
		{
			$this->addError(_msg('Ряд не найден в бд'));
			$this->jump('./');
		}
	}
	public function post($id,$aValues)
	{
		$id = intval($id);
		$aValues = explode("\r\n",$aValues);
		//
		try
		{
			SiteMap::manualOrder($id,$aValues);
		}
		catch (SiteMapException $e)
		{
			$this->addError('Сортировка не удалась. Ряд не найден в бд');
		}
		$this->afterPost();
		$this->jump('./order.php?id='.$id);
	}
	/**
	*   -------------------------------------------------------------------------------------------
	*   Функцию которую можно переопределять ;)
	*   @return
	*   -------------------------------------------------------------------------------------------
	*/
	protected function afterPost()
	{
	}
	protected function formatDesign($aRow = arraY())
	{
		if (!empty($aRow))
		{
			$this->szTitle = 'Сортировка подстраниц раздела "'.$aRow['name'].'"';
		}
		else
		{
			$this->szTitle = 'Сортировка разделов первого уровня';
		}
		$this->aBegin = Sitemap_CMS::selectBegin( Sitemap_CMS::getParents($this->nId), $this->szTitle );
	}
}
?>