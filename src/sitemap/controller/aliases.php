<?php
class SitemapAliasesAdmin extends AdminPage {
	protected $sitemapRow = '';

	public function __construct() {
		parent::__construct();
		$this->addGet('id','showAliases');
		$this->addPost('id,aliasId,delete','delete');
		$this->addPost('id,url,submit','add');
	}
	
	public function showAliases($id) {
		$this->loadSitemapInfo($id);
		// Старт вывода
		$this->outputAliasesHeader();
		// Вывод формы для добавления урла
		$this->outputAddForm();
		// Вывод таблицы урлов, набор галок на удаление
		$this->outputAliasList();
		// Завершаем вывод страницы
		$this->outputAliasesFooter();
		$this->output();
	}
	public function delete($id, $ids) {
		foreach ($ids as $aliasId) {
			Sitemap_History::deleteAliasById($id, $aliasId);
		}
		$this->addAlert('Адрес удален');
		$this->jumpBack();
	}
	public function add($id,$url) {
		if ( empty( $url )) {
			return ;
		}
		if ( $url[0] != '/' ) {
			$url = '/' . $url;
		}
		Sitemap_History::addAlias($id,$url);
		$this->addAlert('Адрес добавлен');
		$this->jumpBack();
	}
	/**
	 Загружает данные об странице
	 */
	private function loadSitemapInfo($id) {
		$this->sitemapRow = Sitemap_Sample::get($id);
		if (empty($this->sitemapRow)) {
			throw new Exception('Load page failed. Sitemap page with id="'.$id.'" not found');
		}
	}
	/**
	 * Выводит форму добавления алиаса
	 */
	private function outputAddForm() {
		$urlField = new CInput();
		$urlField->style = 'width:200px';
		$urlField->name = 'url';
		//
		$design = CMSDesign::getInstance();
		$design->header2('Добавить алиас');
		$design->formBegin();
		$design->tableBegin();
		$design->row2cell('Введите адрес для страницы',$urlField);
		$design->tableEnd();
		$design->submit('submit','Добавить');
		$design->hidden('id',$this->sitemapRow['id']);
		$design->formEnd();
		
	}
	/**
	 * Вывод всех алиасов для страниы
	 */
	private function outputAliasList() {
		$aliasList = Sitemap_History::selectById($this->sitemapRow['id']);
		$tableHeader = array(
			array('&nbsp;',5),
			array('Дата',20),
			array('Наименование',15),
			array('URL',60),
		);
		$design = CMSDesign::getInstance();
		$design->formBegin();
			$design->tableBegin();
			$design->tableHeader($tableHeader);
			foreach ($aliasList as $row) {
				$deleteCheckbox = new CCheckbox();
				$deleteCheckbox->name = 'aliasId[]';
				$deleteCheckbox->id = 'checkbox' . $row['id'];
				$deleteCheckbox->value = $row['id'];
				$design->rowBegin();
				$design->listCell($deleteCheckbox);
				$design->listCell(Date_Helper::getCyrilicViewValue($row['date']));
				$design->listCell(htmlspecialchars($row['name']));
				$design->listCell(htmlspecialchars($row['url']));
				$design->rowEnd();
			}
			$design->tableEnd();
			$design->hidden('id',$this->sitemapRow['id']);
			$design->submit('delete','Удалить');
		$design->formEnd();
	}
	private function outputAliasesHeader() {
		$title = 'Редактирование алиасов для страницы (%s:%s)';
		$title = sprintf($title,$this->sitemapRow['name'],$this->sitemapRow['full_url']);
		$design = CMSDesign::getInstance();
		$design->popupBegin($title);
		$design->header($title);
		$design->contentBegin();
		?>
		<p>На этой странице вы можете отредактировать адреса, обращения к которым приведет
		к открытию текущей страницы</p>
		<?php 
		$design->contentEnd();
	}	
	private function outputAliasesFooter() {
		$design = CMSDesign::getInstance();
		$design->popupEnd();
	}
}