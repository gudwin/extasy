<?php
/**
 * Класс управления отдельным конфигом
 * @author Gisma
 *
 */
class CConfigAdminManagePage extends AdminPage{
	/**
	 * Хранит схему, с которой сейчас работает класс
	 * @var CConfigSchema
	 */
	protected $schema = null;
	public function __construct() {
		parent::__construct();
		$this->addGet('schema','showEditForm');
		$this->addPost('schema,tabsheets','setupTabsheets');
		$this->addPost('schema,name,xtype,title,config','addControl');
		$this->addPost('schema,name,xtype,title','addControl');
		$this->addPost('schema,control,delete','deleteControls');
		$this->addPost('xtype,getAdminForm','getAdminForm');
		$this->addPost('schema,sitemapLink','setupLink');
	}
	public function main() {		
		$this->jump('./index.php');
	}
	/**
	 * Отображает форму управления конфигом
	 * @param string $name
	 */
	public function showEditForm($name) {
		$this->schema = CConfig::getSchema($name);
		$this->output();
	}
	public function addControl($schema,$name,$xtype,$title,$config = null) {
		
		$this->schema = CConfig::getSchema($schema);
		
		$this->schema->addControl($name,$xtype,$title,$config,'');
		
		$this->addAlert('Контрол добавлен');
		$this->jumpBack();	
	}
	public function setupTabsheets($schema,$tabsheets) {
		$this->schema = CConfig::getSchema($schema);
		$this->schema->setTabsheets($tabsheets);
		$this->addAlert('Вкладки установлены');
		$this->jumpBack();	
	}
	
	/**
	 * Удаляет из схемы ($schema) все контролы, имена которых перечислены в массив $controls
	 * @param string $schema
	 * @param array $controls
	 */
	public function deleteControls($schema,$controls) {
		$this->schema = CConfig::getSchema($schema);
		foreach ($controls as $controlName) {
			$this->schema->removeControl($controlName);
		} 
		$this->addAlert('Контролы удалены');
		$this->jumpBack();
	}
	/**
	 * Привязывает конфиг к определенному sitemap-документу
	 * @param string $schema
	 * @param int $sitemapId
	 */
	public function setupLink($schema,$sitemapId) {
		$this->schema = CConfig::getSchema($schema);
		$this->schema->setupSitemapLink($sitemapId);
		$this->addAlert('Свазья с sitemap-документом установлена');
		$this->jumpBack();
	}
	public function output() {
		$tabSheet = array(
			array('id' => 'tab_list', 'title' => 'Список контролов'),
			array('id' => 'tab_sheets', 'title' => 'Вкладки редактирования'),			
			array('id' => 'tab_add', 'title' => 'Добавить контрол'),			
			array('id' => 'tab_sitemap', 'title' => 'Подключить к sitemap'),
		);
		$buttons = array(
			'Редактировать данные' => sprintf('./edit.php?schema=%s',$this->schema->getName())
		);
		// Стартуем вывод
		$this->outputHeader();
		$design = CMSDesign::getInstance();
		$design->decor->buttons( $buttons );
		$design->tabs->sheetsBegin($tabSheet);
		// Вкладка, список полей
			$design->tabs->contentBegin($tabSheet[0]['id']);
				$this->outputControlListForm();
			$design->tabs->contentEnd(); 
		// Вкладка, поля редактирования
			$design->tabs->contentBegin($tabSheet[1]['id']);
				$this->outputTabsheetsForm();
			$design->tabs->contentEnd(); 
		// Добавить контрол
			$design->tabs->contentBegin($tabSheet[2]['id']);
				$this->outputAddControlForm();
			$design->tabs->contentEnd();
		// Отображает форму sitemap ссылки
			$design->tabs->contentBegin($tabSheet[3]['id']);
				$this->outputSitemapLink();
			$design->tabs->contentEnd();
		// Завершаем вывод
		$design->tabs->sheetsEnd();
		$this->outputFooter();
		parent::output();
	}
	public function outputHeader( $begin = array(), $title = '', $script = '',$css = '',$embed = false) {
		$title = sprintf('Управление конфигом "%s"',
				$this->schema->getTitle());
		$begin = array(
			'Управление конфигами' => './index.php',
			$title => '#',
		);
		parent::outputHeader($begin,$title,array(\Extasy\CMS::getResourcesUrl().'extasy/Dashboard/custom_config/manage.js'));
		
	}
	/**
	 * Данный метод вызывает форму редактирования доп. полей у контролов
	 * @param string $xtype
	 */
	public function getAdminForm($xtype) {
		$class = CConfigControlManager::loadControl($xtype);
		print call_user_func(array($class,'outputAdminForm'));
		die();
	}
	/**
	 * Выводит список существующих контролов в конфиге
	 */
	protected function outputControlListForm() {
		// Получаем список всех контролов
		$list = $this->schema->returnControlList();
		//
		
		// Таблица контролов с блоком удаления
		$tableHeader = arraY(array('&nbsp;',5),array('Контрол',95));
		$design = CMSDesign::getInstance();
		$design->forms->begin();
			$design->table->begin();
			$design->table->header($tableHeader);
			foreach ($list as $control) {
				$checkbox = new CCheckbox();
				$checkbox->name = 'control[]';
				$checkbox->value = $control->getName();
				$checkbox->title = '';
				$cell = sprintf('<span class="important">%s</span> - %s',
								$control->getName(),
								$control->getTitle()
				);
				$design->table->rowBegin();
				$design->table->listCell($checkbox);
				$design->table->listCell($cell);
				$design->table->rowEnd();
			}
			$design->table->end();
		$design->forms->hidden('schema',$this->schema->getName());
		$design->forms->submit('delete','Удалить',
						'Вы уверены, что хотите удалить эти контролы?');
		
		$design->forms->end();
	}
	/**
	 * Выводит форму редактирования вкладок в схеме 
	 */
	protected function outputTabsheetsForm() {
		$tabs = $this->schema->selectTabbedControls();
		$values = array();
		foreach ($tabs as $tabTitle => $controlList) {
			$row = array();
			foreach ($controlList as $control) {
				$row[] = $control->getName();
			}
			$values[$tabTitle] = 	implode(',',$row);
		}
		
		// Выводим форму
		$tabControl = new CKeyValueList();
		$tabControl->name = 'tabsheets';
		$tabControl->title = 'Введите имя вкладок и контролов';
		$tabControl->values = $values;

		$design = CMSDesign::getInstance();
		$design->decor->contentBegin();
		?>
		<p>В данном списке Вы можете отредактировать внешний вид формы редатирования конфига. 
		В первой колоке таблицы пишите имя вкладки, а во второй список имен контролов (через запятую).
		</p>
		<p> Если имя вкладки начинается с цифры, то вкладки при редактировании будут отсортированы в алфавитном порядке. 
		Во время вывода на форме редактировании цифры будут удалены.</p>
		<?php 
		$design->decor->contentEnd();
		$design->forms->begin();
		
			$design->table->begin();
			$design->table->fullRow($tabControl);
			$design->table->end();
		$design->forms->hidden('schema',$this->schema->getName());
		$design->forms->submit('setup','Установить вкладки');
		$design->forms->end();
	}
	/**
	 * Отображает форму добавления нового контрола
	 * @todo Сделать поддержку настраиваемых конфигов
	 */
	protected function outputAddControlForm() {
		//
		$controls = CConfigControlManager::selectAll();
		foreach ($controls as $key=>$row) {
			$controls[$key] = array(
				'id' => $key,
				'name' => call_user_func(array($row,'getControlTitle'))
			);
		}
		// Выводим поля для вставки: имя, тайтл, селект выбора типа
		$name = new CInput();
		$name->name = 'name';
		$title = new CInput();
		$title->name = 'title';
		$xtype = new CSelect();
		$xtype->name = 'xtype';
		$xtype->items = $controls;				
		//
		$design = CMSDesign::getInstance();
		$design->forms->begin();
			$design->table->begin();
				$design->table->row2cell('Сист. имя',$name);
				$design->table->row2cell('Подпись к контролу',$title);
				$design->table->row2cell('Тип',$xtype);
			$design->table->end();
			$design->forms->hidden('schema',$this->schema->getName());
			$design->forms->submit('add','Добавить');
		$design->forms->end();
	}
	
	/**
	 * Отображает форму подключения к sitemap
	 */
	protected function outputSitemapLink() {
		require_once LIB_PATH . 'sitemap/control/select.once.php';
		$design = CMSDesign::getInstance();
		
		$sitemapControl = new CSitemapSelectOnce();
		$sitemapControl->name = 'sitemapLink';
		$sitemapControl->value = $this->schema->getSitemapLink();
		$design->forms->begin();
		$design->decor->contentBegin();
		print $sitemapControl->generate();
		$design->decor->contentEnd();
		$sitemapControl->generate();
		$design->forms->submit('submit','Привязать');
		$design->forms->hidden('schema',$this->schema->getName());
		$design->forms->end();
	}		
}