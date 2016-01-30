<?
use \Faid\DB;

//************************************************************//
//                                                            //
//             Базовый класс контроллеров                     //
//       Copyright (c) 2008 Ext-CMS (http://www.ext-cms.com/) //
//               отдел/сектор                                 //
//       Email:    info@gisma.ru (http://www.gisma.ru/)       //
//                                                            //
//  Разработчик: Gisma (31.10.2008)                           //
//  Модифицирован:  31.10.2008  by Gisma                      //
//                                                            //
//************************************************************//

/**
 *
 * Basic front-end model
 * @author Gisma
 *
 */
class RegisteredDocument extends \Extasy\Model\Model {
	const ParentsConfigName  = 'parents';
	const ChildrenConfigName = 'children';

	protected $withSitemap = false;
	/**
	 * Хранит информацию о положении в карте сайта
	 */
	protected $sitemapInfo = null;
	// Свойства работающие в режиме CMS
	/**
	 * Хранит текущие вкладки, на которых будут отображаться данные
	 */
	protected $aCMSTabSheet = array();
	/**
	 * Хранит кнопки которые будут отображены на странице CMS-системы
	 */
	protected $aCMSButton = array();


	public function __construct( $data = array(), $sitemapInfo = array() ) {

		parent::__construct( $data );
		if ( !empty( $sitemapInfo ) ) {
			$this->sitemapInfo = $sitemapInfo;
		}
	}

	/**
	 * Этот метод должен быть реализован в каждом потомке, его действие: создание пустого экземпляра документа
	 * @return Document объект модели
	 */

	public function createEmptyDocument( $parentSitemapId ) {
		//
		$columnName = $this->name;
		// Все равно имя типа совпадает с именем класса
		$columnName->setValue( '' );

		$this->insert( $parentSitemapId );
		$id = $this->columns[ 'id' ]->getValue();


		$columnName->setValue( $this->getLabel( static::labelName). ' ' . $id );
		// Поддержка поля filename по умолчанию
		if ( isset( $this->columns[ 'filename' ] ) ) {
			$columnFileName = $this->columns[ 'filename' ];
			$value          = \convert2lat_url_key( static::ModelName . $id );
			$value          = str_replace( array( '/', '\\' ), '', $value );
			$columnFileName->setValue( $value );
		}
		$this->update();
		//
		return $this;
	}


	/**
	 *
	 * @return int индекс в таблице сайтмап
	 */
	public function getSitemapId() {
		$this->needSitemapData();
		return $this->sitemapInfo[ 'id' ];
	}

	/**
	 *   Возвращает данные для управления в sitemap
	 * @return array(
	 * name => '',
	 * url_key => '',
	 *    );
	 */
	public function getSiteMapData( $key = '' ) {
		$this->needSitemapData();
		if ( !empty( $key ) ) {
			return $this->sitemapInfo[ $key ];
		}
		return $this->sitemapInfo;
	}

	/**
	 * @throws Exception
	 */
	public function needSitemapData() {
		if ( empty( $this->sitemapInfo ) ) {
			$this->sitemapInfo = Sitemap_Sample::getByDocumentId( static::ModelName,
																  $this->columns[ 'id' ]->getValue() );
		}
		if ( empty( $this->sitemapInfo ) ) {
			throw new Exception( 'Current document doesn`t have any sitemap data' );
		}

	}

	/**
	 *
	 * @param int $parentSitemapId индекс в таблице sitemap
	 *
	 * @see Document::insert()
	 */
	public function insert( $parentSitemapId = 0 ) {
		$result = parent::insert();

		if ( $this->withSitemap ) {
			// Получаем сайтмап данные
			$sitemapInfo = $this->returnSitemapParams();
			// Добавляем
			Sitemap::addPage( $sitemapInfo[ 'name' ],
							  $sitemapInfo[ 'url_key' ],
							  static::ModelName,
							  $this->id->getValue(),
							  $parentSitemapId );

			// Загружаем
			$this->needSitemapData();
		}

		//

		return $result;
	}

	public function update() {

		if ( $this->withSitemap ) {
			// Получаем сайтмап данные
			$this->needSitemapData();
			$sitemapInfo = $this->returnSitemapParams();
			Sitemap::updatePage( static::ModelName,
								 $this->id->getValue(),
								 $this->name->getValue(),
								 $sitemapInfo[ 'url_key' ],
								 $this->sitemapInfo[ 'parent' ] );

			// Очищаем текущие сайтмап данные и обновлеяем их
			$this->sitemapInfo = null;
			$this->needSitemapData();
		}

		// Обновляем
		return parent::update();
	}

	/**
	 * (non-PHPdoc)
	 * @see parent::delete()
	 */
	public function delete() {
		// store current id
		$id = $this->getId();

		if ( $this->withSitemap ) {
			try {

				$this->needSitemapData();
				// Вызываем удаление
				$aChild = Sitemap_Sample::selectChild( $this->sitemapInfo[ 'id' ] );
				foreach ( $aChild as $row ) {

					Sitemap::remove( $row[ 'id' ] );

				}
			}
			catch ( SiteMapException $e ) {
			}
		}

		$result = parent::delete();
		if ( $this->withSitemap ) {
			try {
				Sitemap::remove( $this->sitemapInfo[ 'id' ] );
			}
			catch ( SitemapException $e ) {
			}
		}
		// Удаляем данные
		return $result;
	}

	/**
	 * Отображает форму редактирования элемента
	 *
	 * @param $aTabSheet array, хеш-массив имен вкладок и полей внутри их
	 * @param $aButtons  array, хеш-массив кнопок необходимых к выводу
	 */
	public function getAdminUpdateForm( $aTabSheet = array(), $aButtons = array(), $begin = array(), $title = '' ) {
		if ( $this->withSitemap ) {
			$this->needSitemapData();
		}
		// Получаем информацию о типе данных, его полях
		$aType = static::getFieldsInfo();
		// Если пусты вкладки редактировании, то ищем информацию о них в самом типе
		if ( empty( $aTabSheet ) ) {
			if ( !empty( $aType[ 'cms_tabsheets' ] ) ) {
				$aTabSheet = $aType[ 'cms_tabsheets' ];
				// Применяем автоматическую сортировку
				ksort( $aTabSheet );
				// А теперь вырезаем лидирующие цифры
				$tmp       = $aTabSheet;
				$aTabSheet = array();
				foreach ( $tmp as $key => $row ) {
					$key = preg_replace( '#^[0-9]+#', '', $key );

					$aTabSheet[ $key ] = $row;
				}
			} else {
				throw new Exception( 'Form layout empty. It must be defined in child class!' );
			}
		}


		// 
		$nCounter = 0;
		foreach ( $aTabSheet as $key => $row ) {
			$aResultTabSheet[ ] = array(
				'id'    => 'tab_' . $nCounter,
				'title' => $key,
			);
			$nCounter++;
		}

		// Добавляем вкладку о свойствах документа в карте сайта1
		if ( $this->withSitemap ) {
			$this->needSitemapData();
			$aResultTabSheet[ ] = array(
				'id'    => 'tab_sitemap',
				'title' => 'Свойства',
			);
		}

		// Выводим заголовок документа
		$design = CMSDesign::getInstance();

		if ( isset( $this->columns[ 'name' ] ) ) {
			$titleTemplate = 'Редактирование "%s"';
			$szTitle = sprintf( $titleTemplate, cuttext( $this->name->getValue(), 48 ) ); // 48!!!
		} else {
			$szTitle = self::getLabel( self::labelEditItem );
		}


		if ( !empty( $begin ) ) {
			$aBegin = $begin;
		} else {

			$aParent = Sitemap_CMS::getParents( $this->sitemapInfo[ 'id' ] );
			// Вырезаем самих себя из массива
			array_pop( $aParent );
			$aBegin   = Sitemap_CMS::selectBegin( $aParent, $szTitle );
			$submenu  = Sitemap_CMS::generateDocumentSubmenu( $this->getModelName(), $this->sitemapInfo, $aBegin );
			$aButtons = array_merge( $aButtons, $submenu );
		}

		$page = new AdminPage();
		$page->outputHeader( $aBegin, $szTitle );

		// выводим кнопки, если они есть
		if ( !empty( $aButtons ) ) {
			$design->buttons( $aButtons );
		}
		// Вывод всех вкладок
		$design->formBegin();
		$design->submit( 'submit', _msg( 'APPLY' ) );
		$design->tabSheetBegin( $aResultTabSheet );

		foreach ( $aResultTabSheet as $key => $row ) {
			if ( $key == sizeof( $aResultTabSheet ) - 1 ) {
				if ( $this->withSitemap ) {
					// Вывод вкладки свойств-документа
					$this->outputSitemapTabSheet( $row );
					continue;
				} else {

				}

			}
			// Вывод вкладки на редактиование
			$this->outputFormTabSheet( $design,
									   $row,
									   $aTabSheet[ $row[ 'title' ] ],
									   $aType[ 'fields' ],
									   $this->columns );


		}
		$design->tabSheetEnd();
		// Завершаем вывод формы
		if ( $this->withSitemap ) {
			$design->hidden( 'sitemapId', $this->sitemapInfo[ 'id' ] );
		} else {
			$design->hidden( 'typeName', Static::ModelName );
			$design->hidden( 'id', $this->id->getValue() );
		}


		$design->submit( 'submit', _msg( 'APPLY' ) );
		$design->formEnd();
		$page->outputFooter();
	}

	public function isWithSitemap() {
		return $this->withSitemap;
	}

	public function getParseData() {
		$aResult = parent::getParseData();
		if ( !empty( $this->sitemapInfo ) ) {
			$aResult[ 'sitemap' ] = $this->getSiteMapData();
		}
		return $aResult;

	}

	/**
	 *   Возвращает данные для отображения документа в списке
	 * @return
	 */
	public function getPreviewParseData() {
		$aResult = parent::getPreviewParseData();
		if ( !empty( $this->sitemapInfo ) ) {
			$aResult[ 'sitemap' ] = $this->getSiteMapData();
		}
		return $aResult;
	}

	/**
	 * Возвращает документ, являющийся владельцем данного документа
	 * @return RegisteredDocument
	 */
	public function getParent( $asDocument = true ) {
		//
		$this->needSitemapData();
		$parentSitemap = Sitemap_Sample::get( $this->sitemapInfo[ 'parent' ] );
		return $this->returnSitemapInfo( $parentSitemap, $asDocument );


	}

	/**
	 *
	 * Enter description here ...
	 */
	public function getSiblingNext( $asDocument = true ) {
		$this->needSitemapData();
		$sql = 'SELECT * FROM `%s` WHERE `order` > "%d" and `parent` = "%d" ORDER by `order` ASC LIMIT 0,1 ';
		$sql = sprintf( $sql, SITEMAP_TABLE, $this->sitemapInfo[ 'order' ], $this->sitemapInfo[ 'parent' ] );

		$sitemapData = DB::get( $sql );

		// Return as ... 
		return self::returnSitemapInfo( $sitemapData, $asDocument, true );
	}

	/**
	 *
	 * Enter description here ...
	 */
	public function getSiblingPrevious( $asDocument = true ) {
		$this->needSitemapData();
		$sql = 'SELECT * FROM `%s` WHERE `order` < "%d" and `parent` = "%d" ORDER by `order` DESC LIMIT 0,1 ';
		$sql = sprintf( $sql, SITEMAP_TABLE, $this->sitemapInfo[ 'order' ], $this->sitemapInfo[ 'parent' ] );

		$sitemapData = DB::get( $sql );
		// Return as ... 
		return self::returnSitemapInfo( $sitemapData, $asDocument, true );
	}

	///////////////////////////////////////////////////////////////////////////
	//		Static methods
	///////////////////////////////////////////////////////////////////////////

	/**
	 *   Функция-триггер срабатывает после обновления элемента
	 *
	 * @param int $sitemapId индекс в таблице sitemap*
	 *
	 * @return
	 */
	public static function afterUpdate( $sitemapId ) {
	}

	/**
	 * @param $id
	 */
	public static function autoLoad( $id ) {
		$sitemapInfo = Sitemap_Sample::get( $id );
		if ( empty( $sitemapInfo[ 'document_name' ] ) ) {
			throw new NotFoundException( 'Document not found' );
		}
		$document = new $sitemapInfo[ 'document_name' ]( array(), $sitemapInfo );
		$found = $document->get( $sitemapInfo[ 'document_id' ] );
		if ( empty( $found )) {
			throw new NotFoundException('Sitemap document model not found ');
		}
		return $document;
	}

	/**
	 *
	 * Возвращает объект системного реестра
	 * @return SystemRegister
	 */
	public static function getSystemRegisterObject() {
		$register = new SystemRegister( 'System/types/' . static::ModelName );
		return $register;
	}

	/**
	 * Данный метод является фабрикой для загрузки документа
	 *
	 * @param string $documentName
	 * @param int    $documentId
	 *
	 * @return RegisteredDocument
	 */
	public static function loadDocument( $documentName, $documentId ) {
		$validator = new \Extasy\Validators\IsModelClassNameValidator( $documentName );
		if ( !$validator->isValid() ) {
			throw new ForbiddenException( "Not a model: {$documentName}" );
		}
		$modelClass = $documentName;
		$document   = new $modelClass();
		$found      = $document->get( $documentId );
		if ( !$found ) {
			throw new SiteMapException( sprintf( 'Document (%s,%s) not found', $documentName, $documentId ) );
		}
		return $document;

	}

	/**
	 *
	 * Loads document by its sitemap record
	 * @return RegisteredDocument
	 */
	public static function loadBySitemapRecord( $sitemapInfo ) {
		if ( empty( $sitemapInfo[ 'document_name' ] ) ) {
			throw new Exception( 'Not an document' );
		}
		$validator = new \Extasy\Validators\IsModelClassNameValidator( $sitemapInfo[ 'document_name' ] );
		if ( !$validator->isValid() ) {
			throw new ForbiddenException( "Not a model: {$sitemapInfo['document_name']}" );
		}
		$class  = $sitemapInfo[ 'document_name' ];
		$result = new $class();
		$found  = $result->get( $sitemapInfo[ 'document_id' ] );
		if ( empty( $found ) ) {
			throw new Exception( 'Internal error. Document not found, but sitemap info exists ' );
		}
		//
		$result->sitemapInfo = $sitemapInfo;

		return $result;
	}
	///////////////////////////////////////////////////////////////////////////
	// Protected methods
	/**
	 *
	 * Возвращает данные необходимые для сохранения/добавления страницы в таблице sitemap
	 */
	protected function returnSitemapParams() {
		if ( isset( $this->filename ) ) {
			$filename = $this->filename->getValue();
		} elseif ( !is_null( $this->id->getValue() ) ) {
			$filename = convert2lat_url_key( $this->id->getValue() . '_' . $this->name->getValue() );
		} else {
		}
		if ( empty( $filename ) ) {
			$filename = uniqid();
		}
		return array(
			'name'    => $this->name->getValue(),
			'url_key' => $filename
		);
	}

	/**
	 * Отображает вкладку информаци в карте сайта
	 */
	protected function outputSitemapTabSheet( $aTabSheet ) {
		SitemapCMSForms::outputSitemapTabSheet( $this->sitemapInfo, $aTabSheet[ 'id' ] );
	}

	/**
	 * Отображает вкладку полей для редактирования
	 */
	protected function outputFormTabSheet( $design, $aTabSheet, $szFields, $aTypeFields, $aData ) {

		// Вывод полей вкладки
		$design->tabContentBegin( $aTabSheet[ 'id' ] );
		$design->tableBegin();

		// Проверяем что поданный параметр $szFields не является массив
		if ( !is_array( $szFields ) ) {
			// В таком случае мы разрезаем поданное значение по запятым 
			$aTabSheetFields = explode( ',', $szFields );
		} else {
			$aTabSheetFields = $szFields;
		}
		foreach ( $aTabSheetFields as $field ) {
			$field = trim( $field );
			// Фильтруем пустые табы 
			if ( emptY( $field ) ) {
				continue;
			}
			// Получение title поля
			$szTitle = ( is_array( $aTypeFields[ $field ] ) && !empty( $aTypeFields[ $field ][ 'title' ] ) )
				? $aTypeFields[ $field ][ 'title' ] : $field;

			// Определяем есть ли комментарий к полю 
			$helpComment = ( is_array( $aTypeFields[ $field ] ) && !empty( $aTypeFields[ $field ][ 'cms_comment' ] ) )
				? $aTypeFields[ $field ][ 'cms_comment' ] : '';
			if ( isset( $aTypeFields[ $field ][ 'cms_comment' ] ) || isset( $aTypeFields[ $field ][ 'cms_help' ] ) ) {
				$helpComment = isset( $aTypeFields[ $field ][ 'cms_comment' ] ) ? $aTypeFields[ $field ][ 'cms_comment' ] : $aTypeFields[ $field ][ 'cms_help' ];
			} else {
				$helpComment = '';
			}

			$design->row2cell( $szTitle, $aData[ $field ]->getAdminFormValue(), $helpComment );

		}
		$design->tableEnd();
		$design->tabContentEnd();

	}

	/**
	 *
	 * Enter description here ...
	 *
	 * @param bool $asDocument
	 *
	 * @return RegisteredDocument
	 */
	private static function returnSitemapInfo( $parentSitemap, $asDocument = true, $ignoreException = false ) {
		if ( $asDocument ) {

			try {
				return self::loadBySitemapRecord( $parentSitemap );
			}
			catch ( Exception $e ) {
				if ( !$ignoreException ) {
					throw new Exception( $e->getMessage() );
				}
			}
		} else {
			return $parentSitemap;
		}
	}

	public function getAdminInsertForm() {
		parent::getAdminInsertForm();
	}
}

?>