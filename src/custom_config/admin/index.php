<?php
/**
 * Класс 
 * @author Gisma
 *
 */
class CConfigAdminIndexPage extends AdminPage {
	
	public function __construct() {
		parent::__construct();
		$this->addPost('name,delete','delete');
		$this->addPost('name,title','create');
	}
	public function main() {
		$list = CConfig::selectAll();
		$tabSheet = array(
			array('id' => 'tab_list','title' => 'Список всех конфигов'),
			array('id' => 'tab_add','title' => 'Добавить'),
			array('id' => 'tab_delete','title' => 'Удалить')
		);
		$design = CMSDesign::getInstance();
		// Выводим шапку & вкладки
		$this->outputHeader();
		$design->tabs->sheetsBegin($tabSheet);
		// Выводим список конфигов
		$design->tabs->contentBegin($tabSheet[0]['id']);
		$this->outputConfigList($list);
		$design->tabs->contentEnd();
		// Форма создания конфига
		$design->tabs->contentBegin($tabSheet[1]['id']);
		$this->outputCreateConfigForm();
		$design->tabs->contentEnd();
		// форма удаления конфига
		$design->tabs->contentBegin($tabSheet[2]['id']);
		$this->outputRemoveConfigForm($list);
		$design->tabs->contentEnd();
		// Закрываем страницы
		$design->tabs->sheetsEnd();
		$this->outputFooter();
		$this->output();
	}
	/**
	 * Данный метод вызывается, при отсылке формы удаленя конфига
	 * @param string $name
	 */
	public function delete($name) {
		try {
			$schema = CConfig::getSchema($name);
			$schema->delete();
			$this->addAlert(sprintf('Конфиг "%s" успешно удален',$name));
		} 
		catch (Exception $e) {
			$this->addError($e->getMessage());
		}
		$this->jumpBack();
		
	}
	/**
	 * Данный метод обрабатывает форму создания конфига
	 * @param string $name
	 * @param string $title
	 */
	public function create($name,$title) {
		try {
			$schema = CConfig::createSchema($name);
			$schema->updateSchema($name,$title);
			$this->addAlert(sprintf('Конфиг "%s" создан',$name));
		} 
		catch (Exception $e) {
			$this->addError($e->getMessage());
		}
		$this->jumpBack();
	}
	
	
	public function outputHeader( $aBegin = array(),$szTitle = array(),$aScript = array(),$aCSS = array(), $embed = false) {
		$title = 'Управление конфигами';
		$begin = array($title => '#');
		parent::outputHeader($begin,$title);
	}
	protected function outputConfigList($list) {
		$design = CMSDesign::getInstance();
		$tableHeader = array(array('Конфиг',30),array('Редактировать',70));
		$design->table->begin();
		$design->table->header($tableHeader);
		foreach ($list as $row) {
			
			$linkManage = sprintf(
				'<a href="manage.php?edit=1&schema=%s">%s</a> / <span style="color:red">%s</span>',
				$row->getName(),
				$row->getTitle(),
				$row->getName()
			);
			
			$linkEdit = sprintf( '<a href="edit.php?schema=%s">Данные</a>',
				$row->getName());
				
			$design->table->rowBegin();
			$design->table->listCell($linkManage);				
			$design->table->listCell($linkEdit);				
			$design->table->rowEnd();
				
		}
		$design->table->end();
	}
	protected function outputCreateConfigForm() {
		$name = new CInput();
		$name->name = 'name';
		//
		$title = new CInput();
		$title->name = 'title';
		//
		$design = CMSDesign::getInstance();
		$design->forms->begin();
		$design->table->begin();
		$design->table->row2cell('Сис. имя',$name);
		$design->table->row2cell('Подпись для пользователя',$title);
		$design->table->end();
		$design->forms->submit('submit','Создать');
		$design->forms->end();
	}
	protected function outputRemoveConfigForm($list) {
		$select = new CSelect();
		$items = array(array('id' => 0,'name' => 'Ничего не выбрано'));
		foreach ($list as $row) {
			$items[] = array('id' => $row->getName(),'name' => $row->getTitle());
		}
		$select->name = 'name';
		$select->items = $items;
		$design = CMSDesign::getInstance();
		$design->forms->begin();
		$design->table->begin();
		$design->table->row2cell('Выберите конфиг',$select);
		$design->table->end();
		$design->forms->submit('delete','Удалить','Вы уверены, что хотите удалить этот конфиг?');
		$design->forms->end();
	}
	
	
}