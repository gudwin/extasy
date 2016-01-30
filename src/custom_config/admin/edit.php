<?php

/**
 * Страница редактирования данных о конфиге
 * @author Gisma
 *
 */
class CConfigAdminEditPage extends AdminPage {

	/**
	 * @var CConfigSchema
	 */
	protected $schema;
	protected $embed = false;

	public function __construct() {
		parent::__construct();
		$this->addGet( 'schema', 'showForm' );
		$this->addPost( 'schema', 'post' );
	}

	public function post( $schema ) {
		$this->schema = CConfig::getSchema( $schema );

		$this->storeNewValues( $_POST );

		$this->addAlert( 'Сохранено' );
		$this->jumpBack();
	}

	protected function storenewValues( $data ) {
		$this->schema->setValues( $_POST );
		if ( $this->schema->getSitemapLink() ) {
			$sitemapInfo = Sitemap_Sample::get( $this->schema->getSitemapLink() );
			SitemapCMSForms::updateSitemapPageFromPost( $sitemapInfo );
		}
	}

	public function showForm( $schema ) {
		$this->schema = CConfig::getSchema( $schema );
		$this->output();
	}

	public function main() {
		$this->addError( 'На скрипт поданы некорректные параметры' );
		$this->jumpBack();
	}

	/**
	 * Detects how page layout will be rendered
	 *
	 * @param unknown $embedFlag
	 */
	public function setEmbed( $embedFlag ) {
		$this->embed = $embedFlag;
	}

	protected function output() {
		// Вывод начала 
		$controls = $this->schema->selectTabbedControls();
		// Сортируем вкладки по алфавиту
		ksort( $controls );
		$title  = sprintf( 'Редактирование "%s"',
						   $this->schema->getTitle() );
		$sheets = $this->getTabSheets( $controls );

		if ( $this->schema->getSitemapLink() == 0 ) {
			$begin = array( $title => '#' );
		} else {
			$parents = Sitemap_CMS::getParents( $this->schema->getSitemapLink() );
			$begin   = Sitemap_CMS::selectBegin( $parents, $title );
		}

		$this->outputHeader( $begin, $title, array(), array(), $this->embed );
		$this->outputEditingForm( $sheets, $controls );

		parent::output();

	}

	/**
	 * Отображает форму редактирования
	 */
	protected function outputEditingForm( $sheets, $controls ) {
		$sheetsEmpty = false;
		if ( empty( $sheets ) ) {
			$sheetsEmpty = true;
			$sheets      = array(
				array(
					'id'    => 'mainTab',
					'title' => 'Ошибка'
				)
			);
		}
		if ( $this->schema->getSitemapLink() ) {
			$sitemapInfo = Sitemap_Sample::get( $this->schema->getSitemapLink() );
			array_push( $sheets,
						array(
							'id'    => 'sitemapTab',
							'title' => 'Свойства',
						) );
		}

		//
		$design = CMSDesign::getInstance();
		$design->forms->begin();

		// Вывод вкладок
		$design->tabs->sheetsBegin( $sheets );
		// По вкладкам вывод
		$i = 0;
		if ( !empty( $controls ) ) {
			foreach ( $controls as $list ) {
				$design->tabs->contentBegin( $sheets[ $i ][ 'id' ] );
				$design->table->begin();
				foreach ( $list as $control ) {
					$design->table->row2cell( $control->getTitle(), $control->outputInForm() );
				}
				$design->table->end();
				$design->tabs->contentEnd();
				$i++;
			}
		} else {
			$design->tabs->contentBegin( $sheets[ 0 ][ 'id' ] );
			$design->decor->contentBegin();
			printf( 'У данной схемы пока нету вкладок для редактирования<br/>' );
			$auth = CMSAuth::getInstance();
			if ( $auth->isSuperAdmin( UsersLogin::getCurrentUser() ) ) {
				printf( 'Перейти к <a href="%scconfig/manage.php?schema=%s&edit=1">управлению</a> конфигом',
						\Extasy\CMS::getDashboardWWWRoot(),
						$this->schema->getName() );
			}
			$design->decor->contentEnd();
			$design->tabs->contentEnd();
		}
		if ( !empty( $sitemapInfo ) ) {
			$auth = CMSAuth::getInstance();
			if ( $auth->isSuperAdmin( UsersLogin::getCurrentUser() ) ) {
				$link     = sprintf( '<a href="%scconfig/manage.php?schema=%s" target="_blank">Управление конфигом</a>',
									 \Extasy\CMS::getDashboardWWWRoot(),
									 $this->schema->getName()
				);
				$property = array(
					'' => $link
				);
			} else {
				$property = array();
			}
			SitemapCMSForms::outputSitemapTabSheet( $sitemapInfo, $sheets[ sizeof( $sheets ) - 1 ][ 'id' ], $property );
		}
		$design->tabs->sheetsEnd();
		$design->forms->hidden( 'schema', $this->schema->getName() );
		// Вывод конца
		$design->forms->submit( 'submit', 'Сохранить' );
		$design->forms->end();
		$this->outputFooter();
	}

	/**
	 * Возвращает вкладки для отображения формы редактирования
	 *
	 * @param $controls
	 */
	protected function getTabSheets( $controls ) {

		$sheets = array();
		$i      = 0;
		// Формируем массив вкладок
		foreach ( $controls as $tabName => $list ) {
			$tabName   = preg_replace( '#^[0-9]+#', '', $tabName );
			$sheets[ ] = array( 'id' => 'tab' . $i, 'title' => $tabName );
			$i++;
		}
		return $sheets;
	}
}