<?
//************************************************************//
//                                                            //
//           Осуществляет поиск по карте                      //
//       Copyright (c) 2008 Ext-CMS (http://www.ext-cms.com/) //
//               отдел/сектор                                 //
//       Email:    info@gisma.ru (http://www.gisma.ru/)       //
//                                                            //
//  Разработчик: Gisma (03.12.2008)                           //
//  Модифицирован:  03.12.2008  by Gisma                      //
//                                                            //
//************************************************************//

class Sitemap_Controller_Search extends AdminPage
{
	protected $aOutput = array();
	public function __construct()
	{
		parent::__construct();
		$this->addGet('filter,term','searchForJqueryUI');
		$this->addPost('filter,term','searchForJqueryUI');
		$this->addGet('query,start,limit,callback','search');
	}
	public function searchForJqueryUI($filter,$keyword) {
		$result = Sitemap_Sample::search($keyword,0,20, explode(',',$filter));

		foreach ($result as $key=>$row) {
			$result[$key] = array(
				'id' => $row['id'],
				'name' => $row['name'],
				'value' => $row['name'],
				'full_url' => $row['full_url'],
			);
			
		} 
		print json_encode($result);
		die();
	}
	/**
	*   -------------------------------------------------------------------------------------------
	*   Принимает вызов, возвращает результаты поиск
	*   @return
	*   -------------------------------------------------------------------------------------------
	*/
	public function search($keyword,$start,$limit,$callback)
	{
		try
		{
			$aResult = Sitemap_Sample::search($keyword,$start,$limit);
			$nTotal = Sitemap_Sample::getTotalCount();
			$this->aOutput['totalcount'] = $nTotal;
			$this->aOutput['item'] = array();
			foreach ($aResult as $row)
			{

				$this->aOutput['item'][] = array(
					'id' => $row['id'],
					'name' => $row['name'],
					'full_url' => $row['full_url'],
					'date_updated' => $row['unixtimestamp']
					);
			}
		}
		catch (Exception $e)
		{
			$this->aOutput['error'] = 'Ошибка поиска, слишком короткая фраза';
		}
		$this->outputJSONP($callback);
	}
	/**
	*   -------------------------------------------------------------------------------------------
	*   Формирует данные в json форме
	*   @return
	*   -------------------------------------------------------------------------------------------
	*/
	public function outputJSONP($callback)
	{
		print $callback.'('.json_encode($this->aOutput).');';
		Trace::setDisabled();
		die();
	}

}
?>