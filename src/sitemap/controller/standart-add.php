<?php
use \Extasy\CMS;
require_once LIB_PATH.'sitemap/additional/cms.php';
class SitemapStandartAddPage extends AdminPage {
	const ConfigPath = '/System/CMS/StandartAdd';

	public function __construct() {
		parent::__construct();
		$this->addPost('document,place','add');
		$this->addPost('document','showPlaces');
		
	}

	/**
	 * Выводит список доступных для добавления документов
	 */
	public function main() {
		$documents = $this->getDocuments();
		$title = 'Создание нового документа';
		$begin = array(
			$title => '#'
		);
		$tableHeader = array(
			array('&nbsp','10'),
			array('Документ','90')		
		);
		
		//
		$this->outputHeader($begin,$title,CMS::getResourcesUrl().'/extasy/Dashboard/sitemap/standart-add.js');
		//
		$design = CMSDesign::getInstance();
		$design->formBegin();
		$design->tableBegin();
		$design->tableHeader($tableHeader);
		foreach ($documents as $row) {
			//
			$design->rowBegin();
			//
			$radio = sprintf('<input name="document" type="radio" value="%s"/>',htmlspecialchars($row['name']));
			//
			$design->listCell($radio);
			$design->listCell('<strong>'.$row['title'].'</strong>');
			$design->rowEnd();
		}
		$design->tableEnd();
		$design->submit('submit','Выбрать документ');
		$design->formEnd(); 
		//
		$this->outputFooter();
		$this->output();
	}
	public function getDocuments( ) {
		$register = new SystemRegister(self::ConfigPath );
		$data = SystemRegisterHelper::exportData( $register->Documents->getId());
		ksort( $data );

		$result = array();
		foreach ( $data as $key=>$row ) {
			$result[] = array(
				'title' => $key,
				'name' => $row,
			);
		}
		return $result;
	}
	/**
	 * Добавляет документ
	 * @param string $document имя документа
	 */
	public function showPlaces($document ) {

		$documentTitle = call_user_func( array($document,'getLabel'),\Extasy\Model\Model::labelName);
		$title = 'Добавление "%s". Выберите куда публиковать документ';
		$title = sprintf($title,$documentTitle);
		$begin = array(
			'Выбор документа' => './standart-add.php',
			$title => '#'
		);
		//
		$aMove = Sitemap_CMS::whereToMove( $document );
		$this->outputHeader($begin,$title,CMS::getResourcesUrl() . 'extasy/Dashboard/sitemap/standart-add.js');
		$tableHeader = array(
			array('&nbsp;',5),
			array('Cтраница',25),
			array('URL',55),
			);
		$design = CMSDesign::getInstance();
		$design->formBegin();
		$design->TableBegin();
		$design->tableHeader($tableHeader);
		//
		foreach ($aMove as $row)
		{
			$radio = sprintf('<input type="radio" name="place" value="%d" >',$row['id']);
			$design->rowBegin();
			$design->listCell($radio);
			$design->listCell(htmlspecialchars($row['name']));
			$design->listCell(htmlspecialchars($row['full_url']));
			$design->rowEnd();
		}
		if (empty($aMove)) {
			$design->fullrow('<p class="important big">Вы пока не можете создать данный документ, т.к. пока не созданы разделы, куда его можно было бы добавить</p>');
		}
		$design->tableEnd();
		$design->hidden('document',$document);
		$design->submit('submit','Создать');;
		$design->formEnd();
				//
		$this->outputFooter();
		$this->output();
	}
	/**
	 * Создает документ 
	 * @param $document string имя документа
	 * @param $place int индекс документ
	 */
	public function add($document,$place) {
		$place = IntegerHelper::toNatural($place);
		//
		try {
			$validator = new \Extasy\Validators\IsModelClassNameValidator( $document );
			if ( !$validator->isValid() ) {
				throw new ForbiddenException('Not a model');
			}
			$model = new $document();
			$model->createEmptyDocument($place);
		} catch ( Exception $e ) {
			die( $e );
			throw $e;
		}

		
		$this->jump(\Extasy\CMS::getDashboardWWWRoot().'sitemap/edit.php?id='.$model->getSitemapId());
	}
}