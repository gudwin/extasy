<?
//

class Sitemap_PageScriptConfig_Page extends AdminConfig
{
	protected $aSitemap;
	/**
	 * Инициализация
	 */
	public function __construct($nId,$szConfigPath,$aButton = array())
	{
		$nId = intval($nId);
		$aBegin = array();
		$szTitle = '';
		// Получаем запись в карте сайта
		$aSitemap = Sitemap_Sample::get($nId);
		$this->aSitemap = $aSitemap;
		// Проверяем что это вообще скрипт :)
		if (empty($aSitemap['document_name']) && !empty($aSitemap['script']))
		{
			$szTitle = 'Редактирование страницы "'.$aSitemap['name'].'"';

			$aParent = Sitemap_CMS::getParents($aSitemap['id']);

			$aBegin = $this->selectBegin($aParent,$szTitle);
			
		}
		else
		{
			throw new Exception('Sitemap item with id='.$nId.' && config="'.htmlspecialchars($szConfigPath).'" not found');
		}
		if (!file_exists($szConfigPath))
		{
			if (file_exists(SETTINGS_PATH.$szConfigPath))
			{
				$szConfigPath = SETTINGS_PATH.$szConfigPath;
			}
		}
		$this->addPost('sitemap_name,sitemap_url_key,sitemap_move_id','postConfig');

		parent::__construct($aBegin,$szTitle,$szConfigPath,$aButton);
	}
	
	public function main() {
		$auth = CMSAuth::getInstance();
		$design = CMSDesign::getInstance();
		$strings = CMS_Strings::getInstance();
		
		$aData = $this->config->load($this->path);
		$aGenerated = $this->config->generate($aData);
		$aBegin = $this->aBegin;
		$szTitle = $this->szTitle;
		//
		$aTabSheet = $this->generateTabSheetContent($aData,$aGenerated);
		
		$aTabSheet[] = array(
			'id' => 'tab_sitemap',
			'title' => 'Свойства'
			);

		
		$design->begin($aBegin,$szTitle);
		$design->documentBegin();
			$design->header($szTitle);
			if (!empty($this->aButton))
			{
				$design->buttons($this->aButton);
			}
			$this->outputComment();
			$design->formBegin();
			$design->submit('submit',$strings->getMessage('APPLY'));
			$design->tabSheetBegin($aTabSheet);
			foreach ($aTabSheet as $key=>$row)
			{
				
				// Если это не последняя вкладка, выводим из нее контент
				if (($key != sizeof($aTabSheet) - 1))
				{
					$design->tabContentBegin($aTabSheet[$key]['id']);
					$design->tableBegin();
					foreach ($row['item'] as $item)
					{
						$design->row2cell($item['title'],$item['value']);
					}
					$design->tableEnd();
					$design->tabContentEnd();
				} else {
					SitemapCMSForms::outputSitemapTabSheet($this->aSitemap,$aTabSheet[$key]['id'],array(
						'Путь к конфигу' => $this->path,
					));
				}
				
			}
			
			$design->tabSheetEnd();
			$design->submit('submit',$strings->getMessage('APPLY'));
			$design->formEnd();
		$design->documentEnd();
		$design->End();
		$this->output();
	}
	/**
	 * Обновляет данные конфига и данные о скрипте
	 */
	public function postConfig($name,$url_key,$move_id = null)
	{
	
		if (!is_null($move_id)) {
			$this->updateScript($move_id,$name,$url_key);
			SitemapCMSForms::updateSitemapPageFromPost($this->aSitemap);
		}
		parent::post();
	}
	/**
	 * Выводит текущий урл и дополнительное инфо по скрипту
	 */

	
	/**
	 * Формирует хлебные крошки для страницы. 
	 * ОБЩИЙ ПРИНЦИП: перебор всех предков, по url_key пытаемся восстановить скрипт, который выводит подветку карты сайта
	 * если не найден скрипт, то подключаем основной блок aSitemap

	 */
	protected function selectBegin($aParent,$szTitle)
	{
		$aResult = array();
		// Начинаем перебор с самого первого предка 

		// Устанавливаем первым 100%-рабочий URL, основной контрол карты сайта
		$szUrlKey = \Extasy\CMS::getDashboardWWWRoot().'sitemap/index.php?id=';
		
		foreach ($aParent as $row) 
		{
			// Обрабатываем ситуация url_key = "" (нарпимер, главная страница сайта)
			if (empty($row['url_key']))
			{
				$row['url_key'] = 'index';
			}
			// Вырезаем лидирующий слеш
			if ($row['url_key'][0] == '/')
			{
				$row['url_key'] = substr($row['url_key'],1);
			}
			
			// Формируем путь в админке
			$szPath = CP_PATH.$row['url_key'].'/index.php';

			// ОП-ПА! Нашли скрипт, теперь все ссылки будут на него :)
			if (file_exists($szPath)) 
			{
				// Если конечно в файле вызывается класс Sitemap_Controller_Data_List
				$szFileContent = file_get_contents($szPath);
				if (preg_match('#Sitemap_Controller_Data_List#i',$szFileContent))
				{
					$szUrlKey = \Extasy\CMS::getDashboardWWWRoot().$row['url_key'].'/index.php?parent=';
				}
				
			}
			$aResult[$row['name']] = $szUrlKey.$row['id'];
		}
		$aResult[$szTitle] = '#';
		
		return $aResult;
		
	}
	protected function updateScript($to,$name,$url_key)
	{
		$to = intval($to);
		try {
			$aDocument = Sitemap_Sample::get($this->aSitemap['id'],$to);
			if (!empty($aDocument['document_name']))
			{
				throw new Exception('Requested id isn`t script');
			}
			// Обновляем скрипт
			Sitemap::updateScript($this->aSitemap['id'],
				$name,
				$this->aSitemap['script'],
				$url_key,
				(($to >= 0) && ($to != $this->aSitemap['id']))?$to:$this->aSitemap['parent'],
				$this->aSitemap['script_admin_url'],
				$this->aSitemap['script_manual_order']);

			// Обновляем количество детей у старого и нового предка
			Sitemap::updateParentCount($this->aSitemap['id']);
			Sitemap::updateParentCount($to);
		}
		catch (Exception $e) {
			$this->addError('Ошибка при перемещении документа. ',$e->getMessage());
		}
		
	}
	protected function generateTabSheetContent($aConfig,$aGenerated)
	{
		$aResult = array();
		$aTabSheet = array();
		$nCounter = 0;

		foreach ($aConfig as $key=>$row)
		{
			// Если эта запись идет в вкладку
			if (preg_match('#\[(.*?)\](.+)$#',$row['comment'],$aMatch))
			{
				// существует ли первая вкладка
				// или они отличаются заголовками
				if (empty($aTabSheet) || ($aMatch[1] != $aTabSheet['title']))
				{
					if (!empty($aTabSheet))
					{
						$aResult[] = $aTabSheet;
					}

					// создаем новую
					$aTabSheet = array();

					$aTabSheet['id'] = 'tab_'.$nCounter;
					$aTabSheet['title'] = $aMatch[1];
					$aTabSheet['item'] = array(array('title' => $aMatch[2], 'value'  => $aGenerated[$key]));
					
					$nCounter++;
					continue;
				} 
				// значит заголовок последней вкладки равен текущему
				else
				{
					// добавляем в текущую вкладку
					$aTabSheet['item'][] = array(
						'title' => $aMatch[2],
						'value' => $aGenerated[$key]);

				}

			}
			else
			{
				// иначе добавляем в последнюю вкладку, если он существует
				if (empty($aTabSheet))
				{
					// если не существует, создаем вкладку
					// нет, создаем её
					$aTabSheet = array();
					$aTabSheet['id'] = 'tab_'.$nCounter;
					$aTabSheet['title'] = 'Основные данные';
					$aTabSheet['item'] = array();
					
					$nCounter++;
				}
				$aTabSheet['item'][] = array(
					'title' => $row['comment'],
					'value' => $aGenerated[$key]);
					
			}
		}
		// 
		// Сохраняем последнуюю вкладку
		if (!empty($aTabSheet))
		{
			$aResult[] = $aTabSheet; 
		}
		return $aResult;
	}

}
?>