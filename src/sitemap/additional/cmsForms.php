<?php
use \Faid\DB;
require_once CONTROL_PATH . 'input.php';
require_once CONTROL_PATH . 'checkbox.php';
require_once CONTROL_PATH . 'select.php';
/**
 * Генерирует формы для админок sitemap-документоа
 * @author Gisma (2010.11.22)
 *
 */
class SitemapCMSForms {
	protected static $sitemap = null;	
	/**
	 * Отображает вкладку для редактирования sitemap-страницы
	 * @param array $sitemap ряд из таблицы SITEMAP
	 * @param int $tabSheetId вкладка :)
	 */
	public static function outputSitemapTabSheet($sitemap,$tabSheetId,$additionalProperties = array()) {
		
		self::$sitemap = $sitemap;
		$design = CMSDesign::getInstance();
		$szOpenLink = '<a target="_blank" href="http:'.self::$sitemap['full_url'].'" >Открыть</a>';
		$szLink = <<<HTML
				<a href="#" onclick="
				window.open('%ssitemap/move.php?id=%d',
				'_blank',
				'location=no,resizable=no,scrollbars=yes,titlebar=no,toolbar=no,menubar=no,width=800,height=800'); return false;
">Переместить документ</a>
HTML;
		$szLink = sprintf($szLink,\Extasy\CMS::getDashboardWWWRoot(),$sitemap['id']);
		// Выводим
		$design->tabContentBegin($tabSheetId);
		// Вывод блока видимости страницы
		self::outputVisibility($sitemap['visible']);		
		self::outputAliases();
		// Вызов вкладки Sitemap.XML
		self::outputSitemapXML();
		$design->tableBegin();
		$design->row2cell('Создан',$sitemap['date_created']);
		$design->row2cell('Последнее изменение',$sitemap['date_updated']);
		$design->row2cell('Ревизия',$sitemap['revision_count']);
		$design->row2cell('Текущий URL','http:'.$sitemap['full_url'].'  '.$szOpenLink.' '.$szLink);
		if (!empty($sitemap['script'])) {
			$design->row2cell('Путь к скрипту',$sitemap['script']);
		} else {
			self::outputDocumentInfo( $sitemap['document_id'], $sitemap['document_name'] );
		}
		// Дополнительные св-ва
		foreach ($additionalProperties as $key=>$row) {
			$design->row2cell($key,$row);
		}
		$design->tableEnd(); 
		
		// Если это скрипт, то добавляем еще поле
		if (!empty($sitemap['script'])) {
			// Вкладка карты сайта
			$design->header2('Положение в карте сайта');
			$design->tableBegin();
			// 
			self::outputNameFormFields();
			self::outputUrlFormFields();
			self::outputChangeParentFormFields();
			$design->tableEnd();
		}
		
		// Получаем сп
		$design->tabContentEnd();
	}
	protected static function outputSitemapXML()
	{
		$register = new SystemRegister('System/Sitemap/');

		if ($register->get('sitemap.xml')->value)
		{

			require_once CONTROL_PATH . 'input.php';
			require_once CONTROL_PATH . 'select.php';
			//
			$priority = new CInput();
			$priority->name = 'sitemap_xml[priority]';
			$priority->value = self::$sitemap['sitemap_xml_priority'];;
			
			$changefreq = new CSelect();
			$changefreq->items = array(
				array('id' => 'always','name' => 'Всегда'),
				array('id' => 'hourly','name' => 'Почасово'),
				array('id' => 'daily','name' => 'Ежедневно'),
				array('id' => 'weekly','name' => 'Еженедельно'),
				array('id' => 'monthly','name' => 'Ежемесячно'),
				array('id' => 'yearly','name' => 'Ежегодно'),
				array('id' => 'never','name' => 'Никогда'),
				);
			$changefreq->name = 'sitemap_xml[change]';
			$changefreq->current = self::$sitemap['sitemap_xml_change'];

			$design = CMSDesign::getInstance();
			$design->header('Sitemap.XML');
			$design->tableBegin();
			$design->row2cell('Приоритет',$priority->generate());
			$design->row2cell('Частота',$changefreq->generate());
			$design->TableEnd();
		}
	}

	protected static function outputNameFormFields()
	{
		$design = CMSDesign::getInstance();
		$control = new CInput();
		$control->name = 'sitemap_name';
		$control->value = self::$sitemap['name'];
		$control->style = 'width:99%';

		$design->row2cell('Имя в карте сайта',$control->generate());
	}
	/**
	 * Отображает чекбокс для редактирования видимости документа
	 * @param bool $visible
	 */
	protected static function outputVisibility($visible) {
		$checkbox = new CCheckbox();
		$checkbox->value = 1;
		$checkbox->name = 'sitemap_visible';
		$checkbox->title = 'Видима/Невидима';
		$checkbox->checked = !empty($visible);
		$design = CMSDesign::getInstance();
		$design->tableBegin();
		$design->row2cell('Страница видима пользователям',$checkbox);
		$design->tableEnd();
	}
	/**
	 * 
	 * Enter description here ...
	 */
	protected static function outputDocumentInfo ($id,$name) {
		$auth = CMSAuth::getInstance();
		$add2title = '';
		$design = CMSDesign::getInstance();
		$design->row2cell('Документ ', $name . $add2title );
		$design->row2cell('Индекс', $id );
	} 
	
	protected static function outputUrlFormFields()
	{
		$design = CMSDesign::getInstance();
		$control = new CInput();
		$control->name = 'sitemap_url_key';
		$control->value = self::$sitemap['url_key'];
		$control->style = 'width:99%';

		$design->row2cell('Виртуальный путь',$control->generate());
	}
	/**
	 * Выводит ссылку на редактирование алиасов 
	 */
	protected static function outputAliases() {
		$design = CMSDesign::getInstance();
		ob_start();
		$design->createPopupLink('sitemap/aliases.php?id='.self::$sitemap['id'],'Редактировать алиасы');
		$link = ob_get_contents();
		ob_end_clean();
		
		
		$design->tableBegin();
		$design->row2cell('Алиасы данной страницы',$link);
		$design->tableEnd();
	} 
	
	/**
	 * Выводит селект для выбора предка
	 */

	protected static function outputChangeParentFormFields()
	{
		// Формируем список урлов
		$aSitemapUrl = array(
				array('id' => '-2','name' => 'Никуда не переносить')
		);
		// Добавляем parent, предка текущего элемента (если он есть)
		if (!empty(self::$sitemap['parent'])) 
		{
			$aParent = Sitemap_Sample::get(self::$sitemap['parent']);
			$aSitemapUrl[] = array('id' => $aParent['parent'],'name' => '<< Перенести на уровень выше');
			

		}
		// Переносим в дочерние элементы к соседям данного скрипта
		$aSibling = Sitemap_Sample::selectChild(self::$sitemap['parent']);
		if (!empty($aSibling))
		{
			$aSitemapUrl[] = array('id' => -1,'name' => 'Перенести на в дочерние страницы к соседям');
			foreach ($aSibling as $row)
			{
				$aSitemapUrl[] = array('id' => $row['id'],'name' => ' >> '.$row['name']);
			}
		}
		
		// 
		$design = CMSDesign::getInstance();
		$control = new CSelect();
		$control->name = 'sitemap_move_id';
		$control->style = 'width:99%';
		$control->size = '10';
		$control->current = -2;
		$control->values = $aSitemapUrl;

		$design->row2cell('Перенести скрипт',$control->generate());

	}
	/**
	 * Сохраняет данные при редактировании sitemap-страницы
	 * @param array $sitemap
	 */
	public static function updateSitemapPageFromPost($sitemap) {
		self::$sitemap = $sitemap;
		self::updateVisible($sitemap['id']);
		self::updateSitemapXMLFromPost($sitemap['id']);

		self::updateNameAndUrlkey($sitemap['id']);
		// Если это скрипт, то
		if (!empty($_POST['sitemap_url_key'])) {
			$name = $_POST['sitemap_name'];
			$parent = $sitemap['parent'];
			$urlKey = $_POST['sitemap_url_key'];
			if (isset($_POST['sitemap_move_id'])) {
				$sitemapMoveId = intval($_POST['sitemap_move_id']);
				
				if ($sitemapMoveId >= 0) {
					$parent = $sitemapMoveId;
				} 
			} 
			Sitemap::updateScript($sitemap['id'], $name, $sitemap['script'], $urlKey, $parent,$sitemap['script_admin_url'],$sitemap['script_manual_order']);
			if (!empty($sitemapMoveId)) {
				Sitemap::updateParentCount($sitemap['parent']);
				Sitemap::updateParentCount($sitemapMoveId);	
			}						
		}
	}
	protected static function updateParent($sitemapId) {
		
	}
	/**
	 * Обновляет данные о видимости страницы 
	 * @param int $sitemapId
	 * @todo Перенести sql-запрос в отдельный класс
	 */
	protected static function updateVisible($sitemapId) {
		if (!empty($_POST['sitemap_visible'])) {
			$visible = 1;
		} else {
			$visible = 0;
		}
		$sql = 'UPDATE `sitemap` SET `visible`="%d" WHERE `id`="%d"';
		$sql = sprintf($sql,$visible,$sitemapId);
		DB::post($sql);
	}
	/**
	 * Сохраняет обновленные данные об SitemapXML
	 * @param int $id индекс в карте sitemap
	 */
	protected static function updateSitemapXMLFromPost($id) {
		$id = IntegerHelper::toNatural($id);
		$bFound = !empty($_POST['sitemap_xml']) 
					&& !empty($_POST['sitemap_xml']['priority'])
					&& !empty($_POST['sitemap_xml']['change']);

		if ($bFound)
		{
			SitemapXML::update($id,$_POST['sitemap_xml']['priority'],$_POST['sitemap_xml']['change']);

		}
	}

	/**
	 * На основе _POST данных обновляет имя скрипта и часть URI скрипта 
	 */
	protected static function updateNameAndUrlkey($sitemapId) {
		if (!empty($_POST['sitemap_name']) && isset($_POST['sitemap_url_key'])) {
			
			$name = htmlspecialchars($_POST['sitemap_name']);
			$urlKey = htmlspecialchars($_POST['sitemap_url_key']);
			Sitemap::updateScript(  self::$sitemap['id'], 
									$name, 
									self::$sitemap['script'], 
									$urlKey, 
									self::$sitemap['parent'],
									self::$sitemap['script_admin_url'],
									self::$sitemap['script_manual_order']);	
		}
	}
	
}