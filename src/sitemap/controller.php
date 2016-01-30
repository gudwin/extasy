<?php
use \Faid\UParser as UParser;
use \Faid\View\View;

/**
 *
 * Basic controller that works with sitemap
 * @author Gisma
 *
 */
class SiteMapController extends extasyPage {
	const debugCode = 1;

	/**
	 *
	 * If set to true than extasyCMS will skip loadDocument call during page processing
	 * @var bool
	 */
	protected $disableAutoLoad = false;

	/**
	 * @var RegisteredDocument хранит объект текущего документа
	 */
	protected $document = null;

	/**
	 * Если скрипт вызван из-под бд Sitemap, то сюда будет записаны данные текущего урла
	 */
	protected $aUrlInfo = array();

	/**
	 * Путь к шаблону относительно CFG_PATH
	 */
	protected $tplFile = '';

	/**
	 * Массив куда будут складываться данные для парсинг
	 */
	protected $aParse = array();

	/**
	 * Глобальный шаблон
	 * @var string
	 */
	protected $layout = '';

	/**
	 * Содержит текущий шаблон
	 * @var \Faid\View\View
	 */
	protected $view;

	protected $viewHelpers = array();


	public function __construct( $urlInfo = array() ) {
		parent::__construct();
		//
		if ( !empty( $urlInfo ) ) {
			$this->aUrlInfo = $urlInfo;
		}
		$this->addGet( 'id', 'show' );
		Trace::addMessage( 'DB', '<strong>Старт контроллера</strong>' );
	}

	protected function loadDocument( $id ) {
		if ( empty( $this->document )) {
			$validator = new \Extasy\Validators\IsModelClassNameValidator( $this->aUrlInfo[ 'document_name' ] );
			if ( !$validator->isValid() ) {
				throw new ForbiddenException( "Not a model: {$this->aUrlInfo['document_name']}" );
			}
			$model = ( $this->aUrlInfo[ 'document_name' ] );

			$this->document = new $model( array(), $this->aUrlInfo );

			$found = $this->document->get( $id );

			if ( !$found ) {
				throw new NotFoundException( 'Document not found' );
			}
			$this->aParse = $this->document->getParseData();
		}
	}

	public function process() {

		if ( !empty( $this->aUrlInfo[ 'document_name' ] ) ) {
			if ( !isset( $_REQUEST[ 'id' ] ) ) {
				throw new Exception( '`id` parameter not found in request' );
			}
			$id = IntegerHelper::toNatural( $_REQUEST[ 'id' ] );
			if ( !$this->disableAutoLoad ) {

				$this->loadDocument( $id );
			}

		}
		return parent::process();

	}

	/**
	 * Стандартный метод отображения документа на веб-сайт
	 */
	public function show( $id ) {

		if ( empty( $this->aUrlInfo[ 'document_name' ] ) ) {
			return;
		}
        $this->loadDocument( $id );
		$this->aParse = $this->document->getParseData();
		$this->output( $this->tplFile );

	}

	/**
	 * Returns controller document
	 */
	public function getDocument() {
		return $this->document;
	}

	/**
	 * Возвращает текущий layout для страницы
	 * @return string
	 */
	public function getLayout() {
		return $this->layout;
	}

	public function output( $tpl = '', $aData = array(), $aEvent = array() ) {
		$this->tplFile = $tpl;
		// Объединяем данные для парсинга с поступившими данными
		if ( !empty( $aData ) ) {
			$this->aParse = array_merge( $this->aParse, $aData );
		}
		$this->appendGlobalTemplateEventData( $aEvent );
		$this->appendSitemapTemplateData();

		// парсим
		$szContent = $this->render( $this->tplFile );

		if ( !empty( $this->layout ) ) {
			$this->aParse[ 'content_for_layout' ] = $szContent;
			$szContent                            = $this->render( $this->layout );
		}
		$this->htmlResponse = $szContent;

		parent::output();


	}

	public function initilizeDashboardMenu() {
		parent::initilizeDashboardMenu();
		//
		$this->menu->setSitemapInfo( $this->aUrlInfo );
		$this->menu->setTemplate( $this->tplFile );

	}

	protected function appendSitemapTemplateData() {
		$this->aParse[ 'aUrlInfo' ]       = $this->aUrlInfo;
		$this->aParse[ 'currentUrlInfo' ] = $this->aUrlInfo;
	}

	public function goFirstChild() {
		//
		$aMenu = Sitemap_PagesOperations::selectChildWithoutAdditional( $this->aUrlInfo[ 'id' ], 1 );

		if ( empty( $aMenu ) ) {
			throw new NotFoundException();
		}
		// 
		$url = $aMenu[ 0 ][ 'full_url' ];
		$this->jump( $url );
	}


	public static function jumpBack() {
		$url = !empty( $_SERVER[ 'HTTP_REFERER' ] ) ? $_SERVER[ 'HTTP_REFERER' ] : '/';
		self::jump( $url );
	}

	/**
	 *
	 * Enter description here ...
	 *
	 * @param unknown_type $tpl
	 */
	public function render( $tpl = '' ) {
		ini_set( 'include_path', VIEW_PATH );
		$tpl  = $this->detectTplFileName( $tpl );
		$view = new View( $tpl );
		$view->setViewVars( $this->aParse );
		foreach ( $this->viewHelpers as $name => $object ) {
			$view->addHelper( $object, $name );
		}
		$content = $view->render();
		return $content;
	}

	/**
	 *
	 * Set a value inside
	 *
	 * @param string $key
	 * @param mixed  $value
	 */
	public function set( $key, $value = null ) {
		// Support of hash array. Each key will be interpreted as variable name 
		if ( sizeof( func_get_args() ) == 1 ) {
			foreach ( $key as $fieldName => $fieldProperty ) {
				if ( !is_int( $fieldName ) ) {
					$this->set( $fieldName, $fieldProperty );
				}

			}

			return;
		}
		if ( $value instanceof RegisteredDocument ) {
			$this->aParse[ $key ] = $value->getPreviewParseData( true );
		} else {
			$this->aParse[ $key ] = $value;
		}
	}

	protected function appendGlobalTemplateEventData( $events ) {
		array_unshift( $events, 'get_global_template_data' );
		foreach ( $events as $row ) {
			$tmpData = EventController::callEvent( $row, $this->aParse );
			if ( is_array( $tmpData ) ) {
				foreach ( $tmpData as $row ) {
					if ( is_array( $row ) ) {
						$this->aParse = array_merge( $this->aParse, $row );
					}
				}
			}
		}
	}

	public static function cleanSitemapParseData( $data ) {
		if ( is_array( $data ) ) {
			if ( isset( $data[ 'script' ] ) && isset( $data[ 'full_url' ] ) && isset( $data[ 'document_name' ] ) ) {
				unset( $data[ 'url_key' ] );
				unset( $data[ 'date_created' ] );
				unset( $data[ 'date_updated' ] );
				unset( $data[ 'visible' ] );
				unset( $data[ 'order' ] );
				unset( $data[ 'revision_count' ] );
				unset( $data[ 'script_admin_url' ] );
				unset( $data[ 'script_manual_order' ] );
				unset( $data[ 'count' ] );
				unset( $data[ 'sitemap_xml_priority' ] );
				unset( $data[ 'sitemap_xml_change' ] );
			}
		}

		return $data;

	}

	protected function detectTplFileName( $tpl ) {
		if ( file_exists( realpath( $tpl ) ) ) {
			return $tpl;
		} else {
			$testPath = VIEW_PATH . $tpl . '.php';
			$testPath = realpath( $testPath );
			if ( !empty( $testPath ) ) {
				return $testPath;
			}
			$testPath = VIEW_PATH . $tpl . '.tpl';

			return $testPath;
		}
	}

}