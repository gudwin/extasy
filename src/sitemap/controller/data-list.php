<?
use \Faid\DB;
use \Extasy\acl\ModelHelper;

class Sitemap_Controller_Data_List extends AdminPage {

	const ConfigKey                 = 'data_list';
	const ModelDescriptionConfigKey = 'descriptionCallback';
	const CHILD_COUNT               = 5;
	public $standalone = true; // If true that means that page was called outside sitemap directory
	protected $nParent = 0; // Индекс
	protected $nPage = 0; // Номер страницы пейджинга
	protected $aBegin = array(); //
	protected $szTitle = ''; //
	protected $nPagingSize = 10; // Размер пейджинга
	protected $nTotalCount = ''; // Количество страниц
	protected $aItem = array(); // Элементы списка
	protected $aButton = array();
	protected $displayOnlyDocuments = false; // Отображать только документы
	protected $orderCondition = false;

	public function __construct() {
		parent::__construct();
		$this->addGet( 'parent,add', 'add' );
		$this->addGet( 'parent,page', 'processParent' );
		$this->addGet( 'parent', 'processParent' );
		$this->addGet( 'page', 'main' );
		$this->addPost( 'id,page,move_up', 'orderMoveUp' );
		$this->addPost( 'id,page,move_down', 'orderMoveDown' );
		$this->addPost( 'id', 'delete' );
	}

	public function __set( $key, $value ) {
		switch ( $key ) {
			case 'parent':
				$this->nParent = intval( $value );
				break;
			case 'paging_size':
				if ( !is_int( $value ) ) {
					throw new Exception( 'Passed value must be integer' );
				}
				$this->nPagingSize = intval( $value );
				break;
			case 'begin':
				if ( !is_array( $value ) ) {
					throw new Exception( 'Passed value must be array' );
				}
				$this->aBegin = $value;
				break;
			case 'title':
				if ( !is_string( $value ) ) {
					throw new Exception( 'Passed value must be string' );
				}
				$this->szTitle = $value;
				break;
			case 'display_only_documents' :
				$this->displayOnlyDocuments = !empty( $value ) ? true : false;
				break;
			case 'buttons':
				$this->aButton = $value;

				break;
		}
	}

	/**
	 *   -------------------------------------------------------------------------------------------
	 *   Главная функция, отображает собственно списков
	 * @return
	 *   -------------------------------------------------------------------------------------------
	 */
	public function main( $nPage = 0 ) {
		if ( empty( $this->nParent ) ) {
			throw new Exception( 'Parent value can`t be empty' );
		}

		//
		$this->nPage = intval( $nPage );
		//
		$this->getInformation();
		$this->getData();

		$szScript = '<script type="text/javascript" src="' . \Extasy\CMS::getResourcesUrl() . 'extasy/Dashboard/sitemap/order-data-list.js"></script>';
		//

		$design = CMSDesign::getInstance();
		$design->begin( $this->aBegin, $this->szTitle, '', $szScript );
		$design->documentBegin();
		$design->buttons( $this->aButton );
		// Выводим скрипт для обработки select-а
		?>
		<script type="text/javascript">
			<!--
			$(function () {
				$('#document_name').change(function () {
					if (parseInt($(this).val()) != -1) {
						// Переводим на страницу
						window.location = $(this).val();
					}
				});
			});
			//-->
		</script>
		<?php

		// Если у данного документа могут быть дочерние страницы, то выводим их таблицу 
		if ( $this->isCanHaveChildren() ) {

			// Если есть дочерние страницы, то выводим их список
			if ( !empty( $this->aItem ) ) {
				$design->formBegin();
				$this->outputDataTable( $design );
				$design->submit( 'delete', 'Удалить', 'Вы уверены, что хотите удалить выбранные записи?' );
			} else {
				// Если есть дочерние страницы, то выводим предпреждение
				$design->contentBegin();
				?><strong class="important big">У данного документа пока нету дочерних элементов</strong><?
				$design->contentEnd();
			}
		} else {
			$this->jump( sprintf( '../sitemap/edit.php?id=%d', $this->nParent ));
		}

		$design->formEnd();


		$design->documentEnd();
		$design->end();
		$this->output();
	}

	protected function outputDataTable( $design ) {
		$aTableHeader = $this->GetTableHeader();
		$design->paging( $this->nPage, $this->nTotalCount );
		$design->tableBegin();
		$design->tableHeader( $aTableHeader );

		foreach ( $this->aItem as $row ) {
			if ( empty( $row[ 'document_name' ] ) ) {
				if ( $this->displayOnlyDocuments ) {
					continue;
				}
			}
			$design->rowBegin();
			if ( !$row[ 'script' ] ) {
				$design->listCell( '<input type="checkbox" name="id[]" value="' . $row[ 'id' ] . '" />' );
			} else {
				$design->listCell( '' );
			}
			$design->table->orderCell( $row[ 'id' ] );


			$design->listCell( $this->getItemTitle( $row ) );
			$design->listCell( $row[ 'full_url' ] );
			$design->listCell( '<a href="' . \Extasy\CMS::getDashboardWWWRoot() . 'sitemap/edit.php?id=' . $row[ 'id' ] . '">Редактировать</a>' );
			$design->rowEnd();
			$design->tableHr();
		}
		$design->tableEnd();
		$design->paging( $this->nPage, $this->nTotalCount );
		$design->hidden( 'parentId', $this->nParent );

	}

	protected function getPageUrl() {
		return $this->standalone ? 'index.php' : 'page-list.php';
	}

	protected function getItemTitle( $row ) {
		$pageTitle = sprintf( '<a class="itemTitle" href="./%s?parent=%d">%s</a>',
							  $this->getPageUrl(),
							  $row[ 'id' ],
							  $row[ 'name' ] );
		if ( empty( $row[ 'visible' ] ) ) {
			$pageTitle .= ' <span class="important">[Скрыта]</span>';
		}
		$count = \Faid\DBSimple::getRowsCount( SITEMAP_TABLE, ['parent' => $row['id']]);

		if ( !empty( $count ) ) {
			$pageTitle .= sprintf( '<span>, имеет %d %s %s</span>',
								   $count,
								   IntegerHelper::formatWord( $count, 'дочерни', array( 'й', 'х', 'х' ) ),
								   IntegerHelper::formatWord( $count, 'документ', array( '', 'а', 'ов' ) ) );
		}
		if ( !empty( $row[ 'document_name' ] ) ) {
			$pageTitle = $this->userFilterDescription( $row, $pageTitle );
		}
		return $pageTitle;
	}

	protected function userFilterDescription( $row, $description ) {

		$validator = new \Extasy\Validators\ModelConfigValidator( $row[ 'document_name' ],
															   array( 'sitemap',
																	  self::ConfigKey,
																	  self::ModelDescriptionConfigKey
															   ) );
		if ( $validator->isValid() ) {
			$callback = $validator->getData();
			if ( is_callable( $callback ) ) {
				$description = call_user_func( $callback, $row, $description );
			}
		}
		return $description;
	}


	public function delete( $aId ) {
		if ( !is_array( $aId ) ) {
			print 'incorrect request';
			die();
		}
		foreach ( $aId as $nId ) {
			$this->deleteSitemapDocument( $nId );
		}

		$this->jumpBack();
	}

	protected function deleteSitemapDocument( $id ) {
		$sitemapInfo = Sitemap_Sample::get( $id );
		if ( empty( $sitemapInfo ) ) {
			throw new NotFoundException( 'Sitemap with id=' . $id . ' not found' );
		}
		$isEditable = ModelHelper::isEditable( $sitemapInfo[ 'document_name' ] );
		if ( $isEditable ) {
			Sitemap::remove( $id );
		}
	}

	/**
	 *   -------------------------------------------------------------------------------------------
	 *
	 * @return
	 *   -------------------------------------------------------------------------------------------
	 */
	public function add( $nParent, $szDocumentName ) {
		$this->nParent = intval( $nParent );
		$page          = new Sitemap_Controller_Add();
		try {
			$nId = $page->createDocument( $szDocumentName, $this->nParent );
			$this->jump( \Extasy\CMS::getDashboardWWWRoot() . 'sitemap/edit.php?id=' . $nId );
		}
		catch ( Exception $e ) {
			CMSLog::addMessage( __CLASS__, $e );
			$this->addError( $e->getMessage() );
			$this->jumpBack();
		}
	}

	/**
	 *   -------------------------------------------------------------------------------------------
	 *   Вызывается если передан параметр parent, тогда изменяем внутреннее значение parent и
	 *   изменяем заголовок страниц, дополняем хлебные крошки страницы
	 * @return
	 *   -------------------------------------------------------------------------------------------
	 */
	public function processParent( $id, $page = 0 ) {
		try {
			$id            = IntegerHelper::toNatural( $id );
			$aInfo         = Sitemap_Sample::get( $id );
			$aParent       = Sitemap_CMS::getParents( $aInfo[ 'id' ] );
			$this->szTitle = $aInfo[ 'name' ];
			$this->aBegin  = Sitemap_CMS::selectBegin( $aParent, $aInfo[ 'name' ] );

			$this->nParent = intval( $id );
			$this->main( $page );
		}
		catch ( Exception $e ) {
			CMSLog::addMessage( 'sitemap-data-list', $e );
			$this->addError( 'Указанная страница не найдена' );
			$this->jump( \Extasy\CMS::getDashboardWWWRoot() . 'sitemap/' );
		}
	}

	/**
	 *   -------------------------------------------------------------------------------------------
	 *   Получает данные
	 * @return
	 *   -------------------------------------------------------------------------------------------
	 */
	public function getData() {
		try {
			$this->aItem = Sitemap_PagesOperations::selectChildWithAdditional(
												  $this->nParent,
												  $this->nPagingSize,
												  intval( $this->nPage ) * $this->nPagingSize,
												  $this->orderCondition );

			$this->nTotalCount = ceil( Sitemap_PagesOperations::$nTotalCount / $this->nPagingSize );

		}
		catch ( Exception $e ) {
			CMSLog::addMessage( 'data-list', $e );
			throw $e;
		}
		$this->aItem = $this->filterNotEditableDocuments( $this->aItem );
		if ( empty( $this->aItem ) && ( $this->nPage > 0 ) ) {
			$this->jump( 'index.php' . '?page=0' );
		}

	}

	protected function filterNotEditableDocuments( $dataList ) {
		$result = array();
		foreach ( $dataList as $key => $row ) {
			$documentName = $row[ 'document_name' ];
			if ( empty( $documentName ) ) {
				$result[ ] = $row;
				continue;
			}
			$isEditable = ModelHelper::isEditable( $documentName );
			if ( $isEditable ) {
				$result[ ] = $row;
			} else {
			}
		}
		return $result;
	}

	/**
	 * Собирает информацию по текущему элементу (набор кнопок + возможность сортировки)
	 */
	public function getInformation() {
		$aInfo = Sitemap_Sample::get( $this->nParent );
		$szUrl = $this->standalone ? 'index.php' : 'page-list.php';
		if ( empty( $aInfo ) ) {
			throw new Exception( 'Page not found' );
		}

		$this->orderCondition = '`order` asc';

		if ( !empty( $aInfo[ 'script' ] ) ) {
			$aChild = Sitemap_Sample::selectScriptChild( $aInfo[ 'script' ] );
			foreach ( $aChild as $key => $row ) {
				$aChild[ $key ] = $row[ 'document_name' ];
			}
		} else {
			$validator = new \Extasy\Validators\ModelConfigValidator( $aInfo[ 'document_name' ], array('sitemap',RegisteredDocument::ChildrenConfigName ));
			if ( $validator->isValid() ) {
				$aChild = $validator->getData();
			} else {
				$aChild = array();
			}
		}
		// Формируем блок кнопок
		// Добавляем в блок кнопки добавления документа
		// Если более 3 доступных документов для добавления формируем селект
		if ( sizeof( $aChild ) > 3 ) {
			$useSelect = 1;
			require_once CONTROL_PATH . 'select.php';
			$select       = new CSelect();
			$select->name = 'document_name';
			$select->id   = 'document_name';
			$items        = array( array( 'id' => '-1', 'name' => 'Добавить...' ) );
		} else {
		}

		foreach ( $aChild as $documentName ) {

			$title = call_user_func( array( $documentName, 'getLabel' ), \Extasy\Model\Model::labelAddItem );
			$href  = $szUrl . '?parent=' . $this->nParent . '&add=' . $documentName;
			if ( empty( $useSelect ) ) {
				$this->aButton[ $title ] = $href;
			} else {
				$items[ ] = array(
					'id'   => $href,
					'name' => $title,
				);
			}
		}
		if ( !empty( $useSelect ) ) {
			$select->values   = $items;
			$select->current  = '-1';
			$this->aButton[ ] = array(
				'code' => $select
			);
		}

		if ( !empty( $aInfo[ 'document_name' ] ) || !empty( $aInfo[ 'script_admin_url' ] ) ) {
			$this->aButton[ _msg( 'Редактировать' ) ] = \Extasy\CMS::getDashboardWWWRoot() . 'sitemap/edit.php?id=' . $aInfo[ 'id' ];
		}
		$this->aButton[ _msg( 'Сортировать' ) ] = \Extasy\CMS::getDashboardWWWRoot() . 'sitemap/order.php?id=' . $aInfo[ 'id' ];
	}

	/*
	 * Определяет могут ли быть у данного документа дети
	 */
	protected function isCanHaveChildren() {
		$aInfo = Sitemap_Sample::get( $this->nParent );
		// Если это скрипт, то получаем дочерние документы
		if ( !empty( $aInfo[ 'script' ] ) ) {
			$aChild = Sitemap_Sample::selectScriptChild( $aInfo[ 'script' ] );
			return !empty( $aChild );
		} else {
			// Иначе, это документ. Получаем его дочерние элементы
			$validator = new \Extasy\Validators\ModelConfigValidator( $aInfo[ 'document_name' ], array('sitemap',RegisteredDocument::ChildrenConfigName ));
			return $validator->isValid();
		}
	}

	/**
	 * "Поднимает вверх" документ
	 */
	public function orderMoveUp( $id, $nPage = 0 ) {
		$this->nPage = intval( $nPage );
		$aInfo       = Sitemap_Sample::get( $id );

		if ( empty( $aInfo ) || !$this->checkSecurity( $aInfo[ 'id' ], $aInfo[ 'parent' ] ) ) {
			print 'No';
			die();
		}

		$aItem = Sitemap_PagesOperations::selectChildWithoutAdditional( $aInfo[ 'parent' ] );
		$aSort = array();
		foreach ( $aItem as $key => $row ) {

			if ( $row[ 'id' ] == $aInfo[ 'id' ] ) {
				if ( $key > 0 ) {
					$nOld     = array_pop( $aSort );
					$aSort[ ] = $row[ 'id' ];
					$aSort[ ] = $nOld;
				} else {
					$aSort[ ] = $row[ 'id' ];
				}
			} else {
				$aSort[ ] = $row[ 'id' ];
			}

		}
		Sitemap::manualOrder( $aInfo[ 'parent' ], $aSort );

		$this->nParent = $aInfo[ 'parent' ];
		$this->getInformation();
		$this->getData();
		$this->outputDataTable( CMSDesign::getInstance() );
		die();
	}

	/**
	 *
	 */
	public function orderMoveDown( $id, $nPage = 0 ) {
		$this->nPage = intval( $nPage );
		$aInfo       = Sitemap_Sample::get( $id );

		if ( empty( $aInfo ) || !$this->checkSecurity( $aInfo[ 'id' ], $aInfo[ 'parent' ] ) ) {
			print 'No';
			die();
		}
		$aItem = Sitemap_PagesOperations::selectChildWithoutAdditional( $aInfo[ 'parent' ] );

		$aSort  = array();
		$bMoved = false;
		foreach ( $aItem as $row ) {
			if ( ( sizeof( $aSort ) > 0 ) && ( $aSort[ sizeof( $aSort ) - 1 ] == $aInfo[ 'id' ] ) && !$bMoved ) {
				$nOld     = array_pop( $aSort );
				$aSort[ ] = $row[ 'id' ];
				$aSort[ ] = $nOld;
				$bMoved   = true;
			} else {
				$aSort[ ] = $row[ 'id' ];
			}
		}

		Sitemap::manualOrder( $aInfo[ 'parent' ], $aSort );

		$this->nParent = $aInfo[ 'parent' ];

		$this->getInformation();
		$this->getData();
		$this->outputDataTable( CMSDesign::getInstance() );
		die();
	}

	/**
	 * Формирует шапку для таблицы
	 */
	protected function getTableHeader() {

		$aTableHeader = array(
			array( '&nbsp;', 1 ),
			array( '&nbsp;', 1 ),
			array( 'Имя документа', 68 ),
			array( 'URL', 20 ),
			array( 'Редактировать', 10 ),
		);
		return $aTableHeader;
	}

	/**
	 * Проверяе доступен ли эта запись в sitemap, конфигу прописанному к скрипту
	 */
	protected function checkSecurity( $nId, $nParent ) {
		$bFound = false;

		if ( $nId == $this->nParent ) {
			$bFound = true;
		}
		while ( ( $nId != 0 ) && ( $this->nParent != $nId ) ) {
			$aInfo = Sitemap_Sample::get( $nParent );
			// Дошли до корня?
			if ( empty( $aInfo ) ) {
				$aInfo = array(
					'id'     => 0,
					'parent' => 0,
				);
			}
			$nId     = $aInfo[ 'id' ];
			$nParent = $aInfo[ 'parent' ];
			if ( $nId == $this->nParent ) {
				$bFound = true;
				break;
			}
		}
		return $bFound;
	}
}

?>